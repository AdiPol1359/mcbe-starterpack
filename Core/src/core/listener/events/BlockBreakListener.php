<?php

namespace core\listener\events;

use core\caveblock\CaveManager;
use core\item\items\custom\TerrainAxe;
use core\listener\BaseListener;
use core\manager\managers\bossbar\BossbarManager;
use core\manager\managers\privatechest\ChestManager;
use core\manager\managers\quest\QuestManager;
use core\manager\managers\SettingsManager;
use core\manager\managers\SoundManager;
use core\manager\managers\StatTrackManager;
use core\manager\managers\terrain\TerrainManager;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\Armor;
use pocketmine\item\Bow;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\item\Pickaxe;
use pocketmine\item\Sword;
use pocketmine\item\Tool;

class BlockBreakListener extends BaseListener{

    /**
     * @param BlockBreakEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function protectBlockBreak(BlockBreakEvent $e) : void {
        $block = $e->getBlock();

        if($e->getPlayer()->isOp())
            return;

        if(($terrain = TerrainManager::getPriorityTerrain($block->asPosition())) !== null){
            if(!$terrain->isSettingEnabled("block_break")) {
                $e->setDrops([]);
                $e->setCancelled(true);
                $e->getPlayer()->sendTip("§cNiszczenie na tym terenie jest zablokowane!");
            }
        }
    }

    /**
     * @param BlockBreakEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function setDrops(BlockBreakEvent $e) : void {

        if($e->isCancelled())
            return;

        $player = $e->getPlayer();

        if($player->isCreative())
            return;

        $block = $e->getBlock();
        $item = $player->getInventory()->getItemInHand();

        $level = $block->getLevel();

        if(!$e->getItem() instanceof Pickaxe)
            return;

        if($block->getId() === Block::STONE || $block->getId() === Block::COBBLESTONE) {

            $user = UserManager::getUser($player->getName());

            $hasChanged = false;
            $player = $e->getPlayer();

            $quest = $user->getSelectedQuest();

            if($user->isDropEnabled("cobble") === 0)
                $e->setDrops([]);
            else{
                if($user->isSelectedQuest() && $quest->getType() === "DROP_ITEM" && $quest->getItemId() === "cobble" && $block->getDamage() === $quest->getItemDamage()) {
                    $hasChanged = true;
                    UserManager::getUser($player->getName())->addToStatus();
                }
            }

            UserManager::getUser($player->getName())->hasSkill(1) ? $count = 2 : $count = 1;
            UserManager::getUser($player->getName())->hasSkill(6) ? $ore = false : $ore = true;

            if($item->hasEnchantment(Enchantment::FORTUNE))
                $count += $item->getEnchantmentLevel(Enchantment::FORTUNE);

            $chance = 0;

            if($player->hasPermission(ConfigUtil::PERMISSION_TAG."sponsor.drop"))
                $chance += ConfigUtil::SPONSOR_DROP_CHANCE;

            if(UserManager::getUser($player->getName())->isDropEnabled("diamond") && round(rand(0, 1000) / 10, 1) < 1.5 + $chance) {
                if(!$ore)
                    $item = Item::get(Item::DIAMOND, 0, $count);
                else
                    $item = Item::get(Item::DIAMOND_ORE, 0, $count);
                $player->sendPopup("§8(§a§l+".$count."§r§8) §l§bDIAMENT");
                if($player->getInventory()->canAddItem($item))
                    $player->getInventory()->addItem($item);
                else
                    $level->dropItem($player->asVector3(), $item);

                if($user->isSelectedQuest() && $quest->getType() === "DROP_ITEM" && $quest->getItemId() === "diamond") {
                    $hasChanged = true;
                    UserManager::getUser($player->getName())->addToStatus($count);
                }
            }

            if(UserManager::getUser($player->getName())->isDropEnabled("emerald") && round(rand(0, 1000) / 10, 1) < 2.0 + $chance) {
                if(!$ore)
                    $item = Item::get(Item::EMERALD, 0, $count);
                else
                    $item = Item::get(Item::EMERALD_ORE, 0, $count);
                $player->sendPopup("§8(§a§l+".$count."§r§8) §l§aEMERALD");
                if($player->getInventory()->canAddItem($item))
                    $player->getInventory()->addItem($item);
                else
                    $level->dropItem($player->asVector3(), $item);

                if($user->isSelectedQuest() && $quest->getType() === "DROP_ITEM" && $quest->getItemId() === "emerald") {
                    $hasChanged = true;
                    UserManager::getUser($player->getName())->addToStatus($count);
                }
            }
            if(UserManager::getUser($player->getName())->isDropEnabled("gold") && round(rand(0, 1000) / 10, 1) < 3.0 + $chance) {
                if(!$ore)
                    $item = Item::get(Item::GOLD_INGOT, 0, $count);
                else
                    $item = Item::get(Item::GOLD_ORE, 0, $count);
                $player->sendPopup("§8(§a§l+".$count."§r§8) §l§eZLOTO");
                if($player->getInventory()->canAddItem($item))
                    $player->getInventory()->addItem($item);
                else
                    $level->dropItem($player->asVector3(), $item);

                if($user->isSelectedQuest() && $quest->getType() === "DROP_ITEM" && $quest->getItemId() === "gold") {
                    $hasChanged = true;
                    UserManager::getUser($player->getName())->addToStatus($count);
                }
            }
            if(UserManager::getUser($player->getName())->isDropEnabled("iron") && round(rand(0, 1000) / 10, 1) < 2.5 + $chance) {
                if(!$ore)
                    $item = Item::get(Item::IRON_INGOT, 0, $count);
                else
                    $item = Item::get(Item::IRON_ORE, 0, $count);
                $player->sendPopup("§8(§a§l+".$count."§r§8) §l§7ZELAZO");
                if($player->getInventory()->canAddItem($item))
                    $player->getInventory()->addItem($item);
                else
                    $level->dropItem($player->asVector3(), $item);

                if($user->isSelectedQuest() && $quest->getType() === "DROP_ITEM" && $quest->getItemId() === "iron") {
                    $hasChanged = true;
                    UserManager::getUser($player->getName())->addToStatus($count);
                }
            }
            if(UserManager::getUser($player->getName())->isDropEnabled("coal") && round(rand(0, 1000) / 10, 1) < 4.0 + $chance) {
                $item = Item::get(Item::COAL, 0, $count);
                $player->sendPopup("§8(§a§l+".$count."§r§8) §8§lWEGIEL");
                if($player->getInventory()->canAddItem($item))
                    $player->getInventory()->addItem($item);
                else
                    $level->dropItem($player->asVector3(), $item);

                if($user->isSelectedQuest() && $quest->getType() === "DROP_ITEM" && $quest->getItemId() === "coal") {
                    $hasChanged = true;
                    UserManager::getUser($player->getName())->addToStatus($count);
                }
            }

            if(UserManager::getUser($player->getName())->isDropEnabled("money") && round(rand(0, 1000) / 10, 1) < 2.5 + $chance) {
                $count = mt_rand((1 * $count), (3 * $count)) / 100;
                UserManager::getUser($player->getName())->addPlayerMoney($count);
                $player->sendPopup("§8(§a§l+§r§8) §d§l{$count}§r§7zl");

                if($user->isSelectedQuest() && $quest->getType() === "DROP_ITEM" && $quest->getItemId() === "money") {
                    $hasChanged = true;
                    UserManager::getUser($player->getName())->addToStatus($count);
                }
            }

            if($hasChanged)
                if(BossbarManager::getBossbar($player) != null)
                    QuestManager::update($player);
        }
    }

    public function sendMessageItemStatus(BlockBreakEvent $e) : void {

        if($e->isCancelled())
            return;

        $player = $e->getPlayer();
        $item = $player->getInventory()->getItemInHand();

        if(!UserManager::getUser($player->getName())->isSettingEnabled(SettingsManager::ITEM_STATUS))
            return;

        if(!$item instanceof Sword && !$item instanceof Tool && !$item instanceof Bow && !$item instanceof Armor)
            return;

        $levels = [500, 200, 100, 50, 20, 10, 5, 4, 3, 2, 1];
        $damage = $item->getDamage();
        $max_dmg = $item->getMaxDurability();
        $levelDamage = $max_dmg - ($damage + 1);
        foreach($levels as $level) {
            if($level == $levelDamage) {
                if($level != 1)
                    $player->sendMessage(MessageUtil::format("Zostalo ci §9§l$levelDamage §r§7uzyc tym przedmiotem!"));
                else
                    $player->sendMessage(MessageUtil::format("Zostalo ci §9§l$levelDamage §r§7uzycie tym przedmiotem!"));

                SoundManager::addSound($player, $player->asVector3(), "random.pop2");
            }
        }
    }

    public function BlockBreakInCave(BlockBreakEvent $e) {

        $player = $e->getPlayer();
        $block = $e->getBlock();

        if($player->isOp() || $player->hasPermission(ConfigUtil::PERMISSION_TAG."admin.cave"))
            return;

        if(!CaveManager::isInCave($player))
            return;

        $cave = CaveManager::getCave($player);

        if($cave === null)
            return;

        if($cave->isOwner($player->getName()))
            return;

        if(!$cave->isMember($player->getName())){
            $player->sendMessage(MessageUtil::format("Nie mozesz tego zrobic poniewaz nie jestes czlonkiem tej jaskini!"));
            $e->setCancelled(true);
            return;
        }

        if($cave->getCaveSetting("b_b_pl")) {
            $e->setCancelled(true);
            $player->sendMessage(MessageUtil::format("Nie mozesz tego zrobic poniewaz wlasciciel wylaczyl niszczenie dla wszystkich graczy!"));
            return;
        }

        if(!$cave->getPlayerSetting($player->getName(), "b_block") || $block->getId() === Block::BEACON && !$cave->getPlayerSetting($player->getName(), "i_beacon")) {
            $e->setCancelled(true);
            $player->sendMessage(MessageUtil::format("Nie mozesz tego zrobic poniewaz nie masz uprawnien!"));
            return;
        }

        if($cave->getCaveSetting("b_b_off") && !$cave->isOnlineOwner()) {
            $e->setCancelled(true);
            $player->sendMessage(MessageUtil::format("Nie mozesz tego zrobic poniewaz wlasciciela jaskini nie ma na serwerze!"));
            return;
        }

        if(!$cave->getCaveSetting("b_b_time"))
            return;

        if(date("H") >= ($from = $cave->getTimeSetting("f_time")) && date("H") <= ($to = $cave->getTimeSetting("t_time"))) {
            $e->setCancelled(true);
            $player->sendMessage(MessageUtil::format("Nie mozesz tego zrobic poniewaz wlasciel zablokowal niszczenie od godziny §9" . $from . "§7 do godziny §9" . $to));
            return;
        }
    }

    /**
     * @param BlockBreakEvent $e
     *
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function dropToInventory(BlockBreakEvent $e) : void {
        if($e->isCancelled())
            return;

        $player = $e->getPlayer();
        $drops = $e->getDrops();
        $inventory = $player->getInventory();

        foreach($drops as $drop) {
            if($inventory->canAddItem($drop)) {
                $e->setDrops([]);
                $inventory->addItem($drop);
            } else {
                $user = UserManager::getUser($player->getName());
                if($user->isSettingEnabled(SettingsManager::FULL_EQ))
                    $player->sendMessage(MessageUtil::format("Masz pelny ekwipunek item wykopany nie trafil do twojego ekwipunek"));
            }
        }
    }

    public function CaveBorder(BlockBreakEvent $e) {

        if(!CaveManager::isInCave($e->getPlayer()))
            return;

        $block = $e->getBlock();
        $x = $block->getFloorX();
        $z = $block->getFloorZ();

        $border = ConfigUtil::CAVE_BORDER;

        if($x >= $border || $x <= -$border || $z >= $border || $z <= -$border)
            $e->setCancelled(true);
    }

    /**
     * @param BlockBreakEvent $e
     *
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function addDamage(BlockBreakEvent $e) : void{
        if($e->isCancelled())
            return;

        if(!UserManager::getUser($e->getPlayer()->getName())->hasSkill(5))
            return;

        if($e->getItem() instanceof Tool) {
            if(round(rand(0, 1000) / 10, 1) < 5) {
                $player = $e->getPlayer();
                $itemInHand = $player->getInventory()->getItemInHand();
                if($itemInHand->getDamage() <= 3)
                    return;

                $item = $player->getInventory()->getItemInHand()->setDamage($itemInHand->getDamage() - 2);
                $player->getInventory()->setItemInHand($item);
            }
        }
    }

    public function BlockBreakEventBorder(BlockBreakEvent $e) : void{
        $player = $e->getPlayer();
        $block = $e->getBlock();
        if(CaveManager::isInCave($player)){
            if($block->y >= 100)
                $e->setCancelled(true);
        }
    }

    /**
     * @param BlockBreakEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function AddCobbleTop(BlockBreakEvent $e) : void{
        if($e->isCancelled())
            return;

        $block = $e->getBlock();

        if($block->getId() === Block::STONE || $block->getId() === Block::COBBLESTONE)
            UserManager::getUser($e->getPlayer()->getName())->addCobble();
    }

    /**
     * @param BlockBreakEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function addToStatTrack(BlockBreakEvent $e) : void{
        if($e->isCancelled())
            return;

        $item = $e->getItem();

        if(!$item instanceof Tool)
            return;

        if(StatTrackManager::hasStatTrack($item))
            StatTrackManager::addToStatTrack($item);

    }

    /**
     * @param BlockBreakEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function BreakChest(BlockBreakEvent $e) : void{

        if($e->isCancelled())
            return;

        $block = $e->getBlock();

        if($block->getId() !== Block::CHEST)
            return;

        if(!ChestManager::isLocked($block->asPosition()))
            return;

        $player = $e->getPlayer();

        if(!CaveManager::isInCave($player))
            return;

        $chest = ChestManager::getChest($block->asPosition());

        if($chest->getOwner() === $player->getName() || $player->hasPermission(ConfigUtil::PERMISSION_TAG."privatechest") && CaveManager::getCave($player)->getOwner() !== $player->getName()){
            ChestManager::unlockChest($block->asPosition());
            $player->sendMessage(MessageUtil::format("Zniszczono zablokowana skrzynke!"));
            return;
        }

        $e->setDrops([]);
        $e->setCancelled(true);
        $player->sendMessage(MessageUtil::format("Ta skrzynka jest zablokowana!"));
    }

    public function setProtectPos(BlockBreakEvent $e) {
        $player = $e->getPlayer();
        $item = $player->getInventory()->getItemInHand();

        if($item->equalsExact(new TerrainAxe())) {
            $player->sendMessage(MessageUtil::format("Poprawnie zaznaczono pierwsza pozycje!"));
            UserManager::getUser($player->getName())->setPos1($e->getBlock()->asPosition());
            $e->setCancelled(true);
        }
    }
}