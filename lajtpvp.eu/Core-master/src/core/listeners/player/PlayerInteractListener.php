<?php

declare(strict_types=1);

namespace core\listeners\player;

use core\inventories\fakeinventories\RepairInventory;
use core\inventories\fakeinventories\EnchantInventory;
use core\items\custom\TerrainAxe;
use core\items\custom\ThrownTNT;
use core\Main;
use core\managers\ServerManager;
use core\utils\DepositUtil;
use core\utils\MessageUtil;
use core\utils\Settings;
use core\utils\TimeUtil;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Consumable;
use pocketmine\entity\projectile\Throwable;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Armor;
use pocketmine\item\ItemIds;
use pocketmine\item\ProjectileItem;
use pocketmine\item\VanillaItems;

class PlayerInteractListener implements Listener {

    /**
     * @param PlayerInteractEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function protectInteract(PlayerInteractEvent $e) : void {
        $block = $e->getBlock();
        $player = $e->getPlayer();
        $item = $e->getItem();

        if($player->getServer()->isOp($player->getName())) {
            return;
        }

        if(($item instanceof Armor || $item instanceof Throwable || $item instanceof Consumable || $item instanceof ProjectileItem || $item->getId() === VanillaItems::BOW()) && $e->getAction() === $e::RIGHT_CLICK_BLOCK)
            return;

        if(($terrain = Main::getInstance()->getTerrainManager()->getPriorityTerrain($block->getPosition())) !== null){
            if(!$terrain->isSettingEnabled(Settings::$TERRAIN_INTERACT)) {
                if($terrain->getName() === Settings::$PVP_TERRAIN) {
                    if($item->getId() === VanillaItems::BUCKET()) {
                        if($item->getMeta() === 0) {
                            if($block->getId() === BlockLegacyIds::WATER || $block->getId() === BlockLegacyIds::FLOWING_WATER) {
                                if(Main::getInstance()->getWaterManager()->isDelayedWater($block->getPosition()))
                                    return;
                            }
                        }

                        if($item->getMeta() === 8)
                            return;
                    }
                }

                if(!$player->isSneaking()) {
                    if($block->getId() === VanillaBlocks::ENDER_CHEST() || $block->getId() === VanillaBlocks::CRAFTING_TABLE() || $block->getId() === VanillaBlocks::STONE_BUTTON())
                        return;
                }

                $e->cancel();
            }
        }
    }

    /**
     * @param PlayerInteractEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function guildInteract(PlayerInteractEvent $e) : void {
        $block = $e->getBlock();
        $player = $e->getPlayer();
        $item = $e->getItem();

        if($player->getServer()->isOp($player->getName()))
            return;

        if(($item instanceof Throwable || $item instanceof Consumable || $item instanceof ProjectileItem || $item->getId() === ItemIds::BOW) || $e->getAction() !== $e::RIGHT_CLICK_BLOCK)
            return;

        if(($guild = Main::getInstance()->getGuildManager()->getGuildFromPos($block->getPosition())) !== null){
            if(!$guild->existsPlayer($player->getName())) {

                if($block->getId() === BlockLegacyIds::CHEST || $block->getId() === BlockLegacyIds::ENDER_CHEST)
                    return;

                if($item->getId() === ItemIds::BUCKET) {

                    if($item->getMeta() === 0) {
                        if($block->getId() === BlockLegacyIds::WATER || $block->getId() === BlockLegacyIds::FLOWING_WATER) {
                            if(Main::getInstance()->getWaterManager()->isDelayedWater($block->getPosition()))
                                return;
                        }
                    }

                    if($item->getMeta() === 8)
                        return;
                }

                if(!$player->isSneaking()) {
                    if($block->getId() === BlockLegacyIds::ENDER_CHEST || $block->getId() === BlockLegacyIds::WORKBENCH || $block->getId() === BlockLegacyIds::STONE_BUTTON)
                        return;
                }

                $e->cancel();
            }
        }
    }

    /**
     * @param PlayerInteractEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function blockHopperInventory(PlayerInteractEvent $e) : void {
        if($e->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK)
            return;

        $block = $e->getBlock();

        if($block->getId() === BlockLegacyIds::HOPPER_BLOCK) {
            $e->cancel();
        }
    }

    /**
     * @param PlayerInteractEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function anvil(PlayerInteractEvent $e) : void{
        if($e->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK)
            return;

        $player = $e->getPlayer();
        $block = $e->getBlock();

        if($block->getId() === BlockLegacyIds::ANVIL) {
            $e->cancel();

            if($player->isSneaking()) {
                return;
            }

            (new RepairInventory())->openFor([$player]);
        }
    }

    public function blockInventories(PlayerInteractEvent $e) : void {

        if($e->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK)
            return;

        $block = $e->getBlock();
        $blockPos = $block->getPosition();
        $player = $e->getPlayer();

        if($block->getId() === BlockLegacyIds::HOPPER_BLOCK) {
            $e->cancel();
            return;
        }

        if($block->getId() === BlockLegacyIds::ENCHANTING_TABLE) {
            $e->cancel();
            $level = $player->getWorld();
            $count = 0;
            $bx = $blockPos->getX();
            $by = $blockPos->getY();
            $bz = $blockPos->getZ();

            for($i = 0; $i <= 2; $i++) {
                for($ii = 0; $ii <= 2; $ii++) {
                    if($i === 0) {
                        if($level->getBlockAt($bx, $by + $ii, $bz + 2)->getId() === BlockLegacyIds::BOOKSHELF) {
                            $count++;
                        }
                    } else {
                        if($level->getBlockAt($bx + $i, $by + $ii, $bz + 2) === BlockLegacyIds::BOOKSHELF) {
                            $count++;
                        }
                        if($level->getBlockAt($bx - $i, $by + $ii, $bz + 2) === BlockLegacyIds::BOOKSHELF) {
                            $count++;
                        }
                    }
                }
            }

            for($i = 0; $i <= 2; $i++) {
                for($ii = 0; $ii <= 2; $ii++) {
                    if($i === 0) {
                        if($level->getBlockAt($bx, $by + $ii, $bz - 2) === BlockLegacyIds::BOOKSHELF) {
                            $count++;
                        }
                    } else {
                        if($level->getBlockAt($bx + $i, $by + $ii, $bz - 2) === BlockLegacyIds::BOOKSHELF) {
                            $count++;
                        }
                        if($level->getBlockAt($bx - $i, $by + $ii, $bz - 2) === BlockLegacyIds::BOOKSHELF) {
                            $count++;
                        }
                    }
                }
            }

            for($i = 0; $i <= 1; $i++) {
                for($ii = 0; $ii <= 2; $ii++) {
                    if($i === 0) {
                        if($level->getBlockAt($bx + 2, $by + $ii, $bz) === BlockLegacyIds::BOOKSHELF) {
                            $count++;
                        }
                    } else {
                        if($level->getBlockAt($bx + 2, $by + $ii, $bz + $i) === BlockLegacyIds::BOOKSHELF) {
                            $count++;
                        }
                        if($level->getBlockAt($bx + 2, $by + $ii, $bz - $i) === BlockLegacyIds::BOOKSHELF) {
                            $count++;
                        }
                    }
                }
            }

            for($i = 0; $i <= 1; $i++) {
                for($ii = 0; $ii <= 2; $ii++) {
                    if($i === 0) {
                        if($level->getBlockAt($bx - 2, $by + $ii, $bz) === BlockLegacyIds::BOOKSHELF) {
                            $count++;
                        }
                    } else {
                        if($level->getBlockAt($bx - 2, $by + $ii, $bz + $i) === BlockLegacyIds::BOOKSHELF) {
                            $count++;
                        }
                        if($level->getBlockAt($bx - 2, $by + $ii, $bz - $i) === BlockLegacyIds::BOOKSHELF) {
                            $count++;
                        }
                    }
                }
            }

            (new EnchantInventory($count))->openFor([$player]);
        }
    }

    public function interactTnt(PlayerInteractEvent $e) : void {

        $player = $e->getPlayer();
        $item = $e->getItem();
        $block = $e->getBlock();

        if(($item->getId() !== ItemIds::FLINT_STEEL && $item->getId() !== ItemIds::DIAMOND_SWORD) || $block->getId() !== BlockLegacyIds::TNT)
            return;

        $user = Main::getInstance()->getUserManager()->getUser($player->getName());

        if(($guild = Main::getInstance()->getGuildManager()->getGuildFromPos($block->getPosition())) !== null) {
            if($guild->existsPlayer($player->getName())) {
                if(!$user->hasLastData(Settings::$TNT_ON_SELF_TERRAIN)) {
                    $player->sendMessage(MessageUtil::format("Nie mozesz podpalac tnt na terenie wlasnej gildii!"));
                    $user->setLastData(Settings::$TNT_ON_SELF_TERRAIN, (time() + Settings::$TNT_ON_SELF_TERRAIN_TIME), Settings::$TIME_TYPE);
                }

                $e->cancel();
            }
        }
    }

    public function interactWithTnt(PlayerInteractEvent $e) : void {

        if($e->getAction() !== $e::RIGHT_CLICK_BLOCK)
            return;

        $player = $e->getPlayer();
        $item = $e->getItem();
        $block = $e->getBlock();

        if(!Main::getInstance()->getServerManager()->isSettingEnabled(ServerManager::TNT)) {
            if($item->getId() === ItemIds::FLINT_AND_STEEL && $block->getId() === BlockLegacyIds::TNT) {
                if(($guild = Main::getInstance()->getGuildManager()->getGuildFromPos($block->getPosition())) !== null) {
                    if($guild->isTntEnabled())
                        return;
                }

                $player->sendMessage(MessageUtil::format("Tnt jest aktualnie wylaczone!"));
                $e->cancel();
            }
        }
    }

    /**
     * @param PlayerInteractEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function borderInteract(PlayerInteractEvent $e) : void {
        $block = $e->getBlock()->getPosition();

        $border = Settings::$BORDER_DATA["border"];

        if($block->x >= $border || $block->x <= -$border || $block->z >= $border || $block->z <= -$border)
            $e->cancel();
    }

    public function antyLogout(PlayerInteractEvent $e) : void {
        $player = $e->getPlayer();
        $block = $e->getBlock();
        $user = Main::getInstance()->getUserManager()->getUser($player->getName());

        if(!$user || !$user->hasAntyLogout()) {
            return;
        }

        if($block->getId() === BlockLegacyIds::ENDER_CHEST || $block->getId() === BlockLegacyIds::CHEST || $block->getId() === BlockLegacyIds::TRAPPED_CHEST || $block->getId() === BlockLegacyIds::ITEM_FRAME_BLOCK || $block->getId() === BlockLegacyIds::CRAFTING_TABLE)
            $e->cancel();
    }

    public function depositUpdate(PlayerInteractEvent $e) : void {
        $player = $e->getPlayer();
        $item = $e->getItem();

        if($item->getId() !== ItemIds::ENCHANTED_GOLDEN_APPLE && $item->getId() !== ItemIds::GOLDEN_APPLE && $item->getId() !== ItemIds::ENDER_PEARL && $item->getId() !== ItemIds::SNOWBALL && $item->getId() !== ItemIds::ARROW && !$item->equals(new ThrownTNT()))
            return;

        $user = Main::getInstance()->getUserManager()->getUser($player->getName());

        if(!$user)
            return;

        $terrains = Main::getInstance()->getTerrainManager()->getTerrainsFromPos($player->getPosition());

        foreach($terrains as $terrain) {
            if($terrain->getName() === Settings::$SPAWN_TERRAIN)
                return;
        }

        $deposit = DepositUtil::getDepositData();

        foreach($player->getInventory()->getContents(false) as $item) {
            foreach($deposit as $stat => $data) {
                if($item->equals($data["item"], false, false))
                    $deposit[$stat]["count"] += $item->getCount();
            }
        }

        if(!empty($deposit)) {
            foreach($deposit as $stat => $data) {
                if($data["count"] > $data["limit"]) {
                    $different = ($data["count"] - $data["limit"]);
                    $player->getInventory()->removeItem($data["item"]->setCount($different));
                    $user->getStatManager()->addStat($stat, $different);

                    $player->sendMessage(MessageUtil::format("§cTwoj nadmiar zostal przeniesiony do depozytu §8(§c".$data["normalName"]." x".$different."§8)"));
                }
            }
        }
    }

    public function checkChest(PlayerInteractEvent $e) : void {
        if($e->getAction() !== $e::RIGHT_CLICK_BLOCK)
            return;

        $block = $e->getBlock();

        if($block->getId() !== BlockLegacyIds::CHEST)
            return;

        $player = $e->getPlayer();

        if($player->getServer()->isOp($player->getName())) {
            return;
        }

        $user = Main::getInstance()->getUserManager()->getUser($player->getName());
        $stat = $user->getStatManager();
        
        $timePlayed = ($stat->getStat(Settings::$STAT_SPEND_TIME) + ($stat->getStat(Settings::$STAT_SPEND_TIME) + ($player->getServer()->getPlayerExact($user->getName()) ? (time() - $stat->getStat(Settings::$STAT_LAST_JOIN_TIME)) : 0)));

        if($timePlayed <= Settings::$CHEST_BLOCK_OPEN) {
            if(!$user->hasLastData(Settings::$LAST_OPENED_CHEST)) {
                $player->sendMessage(MessageUtil::format("Otwierac skrzynki mozna dopiero §e30 §7minut po dolaczeniu na serwer, musisz jeszcze odczekac " . TimeUtil::convertIntToStringTime((Settings::$CHEST_BLOCK_OPEN - $timePlayed), "§e", "§7")));
                $user->setLastData(Settings::$LAST_OPENED_CHEST, (time() + Settings::$LAST_OPENED_CHEST_TIME), Settings::$TIME_TYPE);
            }

            $e->cancel();
        }
    }

    public function mapInteract(PlayerInteractEvent $e) : void {
        $item = $e->getItem();

        if($item->getId() === ItemIds::MAP || $item->getId() === ItemIds::FILLED_MAP)
            $e->cancel();
    }

    public function chestLocker(PlayerInteractEvent $e) : void {
        $player = $e->getPlayer();
        $block = $e->getBlock();

        if(($chestLocker = Main::getInstance()->getChestLockerManager()->getLocker($block->getPosition()))) {
            if($chestLocker->canOpen($player))
                return;

            $e->cancel();
            $player->sendMessage(MessageUtil::format("Ta skrzynka jest zablokowana!"));
        }
    }
    
    public function setPos(PlayerInteractEvent $e) : void {

        if($e->getAction() !== $e::RIGHT_CLICK_BLOCK)
            return;

        $player = $e->getPlayer();

        if(!$player->getServer()->isOp($player->getName())) {
            return;
        }

        $user = Main::getInstance()->getUserManager()->getUser($player->getName());
        $item = $e->getItem();

        if($item->equalsExact(new TerrainAxe())) {
            if($user->hasLastData(Settings::$PROTECT_TERRAIN_INTERACT))
                return;

            $user->setLastData(Settings::$PROTECT_TERRAIN_INTERACT, Settings::$PROTECT_TERRAIN_INTERACT_TIME, Settings::$TIME_TYPE);

            $player->sendMessage(MessageUtil::format("Poprawnie zaznaczono druga pozycje!"));
            $user->getTerrainManager()->setPos2($e->getBlock()->getPosition());
        }
    }
}