<?php

declare(strict_types=1);

namespace core\listeners\block;

use core\guilds\GuildPlayer;
use core\items\custom\FastPickaxe;
use core\items\custom\PremiumCase;
use core\items\custom\StoneGenerator;
use core\items\custom\TerrainAxe;
use core\Main;
use core\managers\StoneGeneratorManager;
use core\tasks\sync\StoneGeneratorTask;
use core\utils\MessageUtil;
use core\utils\PermissionUtil;
use core\utils\Settings;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\item\ItemIds;
use pocketmine\item\TieredTool;
use pocketmine\player\GameMode;

class BlockBreakListener implements Listener {

    /**
     * @param BlockBreakEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function protectBlockBreak(BlockBreakEvent $e) : void {
        $block = $e->getBlock();
        $player = $e->getPlayer();

        if($player->getServer()->isOp($player->getName())) {
            return;
        }

        if(($terrain = Main::getInstance()->getTerrainManager()->getPriorityTerrain($block->getPosition())) !== null){
            if(!$terrain->isSettingEnabled(Settings::$TERRAIN_BREAK_BLOCK)) {
                $e->cancel();
            }
        }
    }

    public function setProtectPos(BlockBreakEvent $e) {
        $player = $e->getPlayer();

        if(!$player->getServer()->isOp($player->getName())) {
            return;
        }

        $item = $player->getInventory()->getItemInHand();

        if($item->equalsExact(new TerrainAxe())) {
            $player->sendMessage(MessageUtil::format("Poprawnie zaznaczono pierwsza pozycje!"));
            Main::getInstance()->getUserManager()->getUser($player->getName())->getTerrainManager()->setPos1($e->getBlock()->getPosition());
            $e->cancel();
        }
    }

    /**
     * @param BlockBreakEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function breakOnGuild(BlockBreakEvent $e) : void {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        $terrain = Main::getInstance()->getGuildManager()->getGuildFromPos($block->getPosition());

        if(!$terrain)
            return;

        if(PermissionUtil::has($player, Settings::$PERMISSION_TAG."admin.guild.interact"))
            return;

        $terrainPlayer = $terrain->getPlayer($player->getName());

        if(!$terrainPlayer) {
            $e->cancel();
            $player->sendMessage(MessageUtil::format("Nie mozesz niszczyc na cudzym terenie!"));
            if($player->isSurvival())
                $player->setGamemode(GameMode::ADVENTURE());
            return;
        }

        if(!$terrainPlayer->getSetting(GuildPlayer::BLOCK_BREAK)) {
            $e->cancel();
            $player->sendMessage(MessageUtil::format("Nie masz uprawnien aby niszczyc na terenie!"));
        }
    }
    
    /**
     * @param BlockBreakEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function setDrops(BlockBreakEvent $e) : void {
        $player = $e->getPlayer();

        if($e->isCancelled() || $player->isCreative()) {
            return;
        }

        $block = $e->getBlock();
        $item = $player->getInventory()->getItemInHand();

        if($block->getId() === BlockLegacyIds::STONE && $block->getMeta() === 0) {
            $dropManager = Main::getInstance()->getDropManager();
            $user = Main::getInstance()->getUserManager()->getUser($player->getName());

            if(!$user->getDropManager()->isDropEnabled($dropManager->getDropById(11))) {
                $e->setDrops([]);
            }

            $player->getXpManager()->addXp(5, false);

            $possibleDrops = [];

            $rand = round(mt_rand() / mt_getrandmax() * 100, 1);

            foreach($dropManager->getDrop() as $drop) {
                $chance = 0;

                foreach($drop->getBonuses() as $permission => $bonus) {
                    if(PermissionUtil::has($player, Settings::$PERMISSION_TAG . $permission)) {
                        $chance += $bonus;
                    }
                }

                if(Main::getInstance()->getTurboDropManager()->isTurboDropEnabledFor($player->getName()))
                    $chance += 10;

                if($drop->getDrop()["what"]->equals(new PremiumCase()))
                    $chance = 0;
                // TODO particle podczas kopania wlaczanie i wylaczanie

                if ($rand <= (float) ($drop->getChance() + $chance)) {
                    $possibleDrops[] = $drop;
                }
            }

            if (!empty($possibleDrops)) {
                $userBackpack = $user->getBackpackManager();

                shuffle($possibleDrops);
                $randomData = $possibleDrops[array_rand($possibleDrops)];
                $maxAmount = $randomData->getAmount()["max"];

                $continue = false;

                if(!$user->getDropManager()->isDropEnabled($randomData) || $randomData->isDefault()) {
                    return;
                }

                foreach($randomData->getTools() as $tool) {
                    if($tool->getId() === $item->getId()) {
                        $continue = true;
                    }
                }

                if(!$continue) {
                    return;
                }

                if($randomData->isFortune()) {
                    if($item->hasEnchantment(($fortuneEnchantment = EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::FORTUNE)))) {
                        $maxAmount += (mt_rand(0, ($item->getEnchantmentLevel($fortuneEnchantment) - 1) < 0 ? 0 : $item->getEnchantmentLevel($fortuneEnchantment)));
                    }
                }

                $randomAmount = mt_rand($randomData->getAmount()["min"], $maxAmount);

                $item = clone $randomData->getDrop()["what"];
                $item->setCount($randomAmount);

                $message = $randomData->getMessage();
                $message = str_replace("{COUNT}", (string)$randomAmount, $message);
                $message = str_replace("{COLOR}", $randomData->getColor(), $message);
                $message = str_replace("{NAME}", $randomData->getName(), $message);

                $player->sendPopup($message);

                $player->getXpManager()->addXp($randomData->getExpDrop(), false);

                if($player->getInventory()->canAddItem($item)) {
                    $player->getInventory()->addItem($item);
                }
                else {
                    if(!$user->hasLastData(Settings::$BACKPACK_DROP)) {
                        $player->sendMessage(MessageUtil::format("§eMasz pelny ekwipunek dlatego drop zostal przeniesiony do plecaka §8(§e/plecak§8) lub depozytu §8(§e/depozyt§8)"));
                        $user->setLastData(Settings::$BACKPACK_DROP, (time() + Settings::$BACKPACK_DROP_TIME), Settings::$TIME_TYPE);
                    }

                    if(!$randomData->getDeposit()["depositItem"]) {
                        $size = $userBackpack->getMaxBackpackSize();

                        $freeSize = $size - ($userBackpack->getItemsCountInBackpack() + $item->getCount());

                        if(PermissionUtil::has($player, Settings::$PERMISSION_TAG."backpack.unlimited")) {
                            $size = -1;
                            $freeSize = -1;
                        }

                        if($size !== -1 && $size < $userBackpack->getItemsCountInBackpack())
                            return;

                        if($freeSize < 0 && $freeSize !== -1)
                            $item->setCount($freeSize);

                        if($item->getCount() <= 0)
                            return;

                        $userBackpack->addItem($randomData, $item->getCount());
                    } else {
                        $depositName = $randomData->getDeposit()["depositName"];
                        $user->getStatManager()->addStat($depositName, $item->getCount());
                    }
                }
            }
        }
    }

    /**
     * @param BlockBreakEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function stoneGenerator(BlockBreakEvent $e) : void {
        if($e->isCancelled()) {
            return;
        }

        $block = $e->getBlock();
        $item = $e->getItem();
        $player = $e->getPlayer();

        if($block->getId() === BlockLegacyIds::STONE && StoneGeneratorManager::isStoneGenerator(clone $block->getPosition())) {
            if($item->getId() !== ItemIds::GOLD_PICKAXE)
                Main::getInstance()->getScheduler()->scheduleDelayedTask(new StoneGeneratorTask($block->getPosition()), 20 * Settings::$STONE_REGENERATION);
            else {
                $player->sendMessage(MessageUtil::format("Zniszczyles stoniarke!"));
                $e->setDrops([VanillaBlocks::STONE()->asItem()]);
                return;
            }
        }

        if($block->getId() === BlockLegacyIds::END_STONE && !$player->isCreative()) {
            if($item->getId() !== ItemIds::GOLD_PICKAXE) {
                $player->sendMessage(MessageUtil::format("Stoniarke mozna zniszczyc tylko zlotym kilofem!"));
                $e->cancel();
            } else
                $e->setDrops([new StoneGenerator()]);
        }
    }

    /**
     * @param BlockBreakEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function statsBreakBlock(BlockBreakEvent $e) : void {
        if($e->isCancelled()) {
            return;
        }

        Main::getInstance()->getUserManager()->getUser($e->getPlayer()->getName())->getStatManager()->addStat(Settings::$STAT_BREAK_BLOCKS);
    }

    public function enderChestBreak(BlockBreakEvent $e) : void {
        if($e->getBlock()->getId() === BlockLegacyIds::ENDER_CHEST)
            $e->setDrops([VanillaBlocks::ENDER_CHEST()->asItem()]);
    }

    public function heartTerrain(BlockBreakEvent $e) : void {
        $block = $e->getBlock();

        if(($guild = Main::getInstance()->getGuildManager()->getGuildFromPos($block->getPosition())) !== null) {
            if($guild->isInHeart($block->getPosition())) {
                $e->cancel();
            }
        }
    }

    public function blockBreakFastPickaxe(BlockBreakEvent $e) : void {
        $player = $e->getPlayer();
        $item = $e->getItem();
        $user = Main::getInstance()->getUserManager()->getUser($player->getName());

        if(!$user) {
            return;
        }

        if($item->equals((new FastPickaxe())->__toItem(), false) && $item instanceof TieredTool) {
            if($item->getMeta() >= ($item->getMaxDurability() - 25)) {

                if(!$user->hasLastData(Settings::$LOW_DAMAGE_FASTPICKAXE)) {
                    $user->setLastData(Settings::$LOW_DAMAGE_FASTPICKAXE, (time() + Settings::$LOW_DAMAGE_FASTPICKAXE_TIME), Settings::$TIME_TYPE);
                    $player->sendMessage(MessageUtil::format("Zablokowano niszczenie blokow §ekilofem §e6§8/§e3§8/§e3 §7poniewaz on ma tylko §e" . ($item->getMaxDurability() - $item->getDamage()) . " §7uzyc"));
                }

                $e->cancel();
            }
        }
    }

    /**
     * @param BlockBreakEvent $e
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function dropToInventory(BlockBreakEvent $e) : void {
        if($e->isCancelled()) {
            return;
        }

        $player = $e->getPlayer();
        $drops = $e->getDrops();
        $inventory = $player->getInventory();

        foreach($drops as $drop)
            $inventory->addItem($drop);

        $e->setDrops([]);
    }

    /**
     * @param BlockBreakEvent $e
     * @priority LOW
     * @ignoreCancelled true
     */
    public function borderBreak(BlockBreakEvent $e) : void {
        $block = $e->getBlock()->getPosition();

        $border = Settings::$BORDER_DATA["border"];

        if($block->x >= $border || $block->x <= -$border || $block->z >= $border || $block->z <= -$border) {
            $e->cancel();
        }
    }

    public function oreDropDisable(BlockBreakEvent $e) : void {
        $block = $e->getBlock();

        $ids = [14,15,16,21,56,73,74,129,153];

        if(in_array($block->getId(), $ids)) {
            $e->setDrops([]);
            $e->setXpDropAmount(0);
        }
    }

    public function chestLocker(BlockBreakEvent $e) : void {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        if(($chestLocker = Main::getInstance()->getChestLockerManager()->getLocker($block->getPosition()))) {
            if($chestLocker->canOpen($player)) {
                Main::getInstance()->getChestLockerManager()->removeChestLocker($chestLocker->getId());
                $player->sendMessage(MessageUtil::format("Usunales zablokowana skrzynke"));
                return;
            }

            $e->cancel();
            $player->sendMessage(MessageUtil::format("Ta skrzynka jest zablokowana!"));
        }
    }
}