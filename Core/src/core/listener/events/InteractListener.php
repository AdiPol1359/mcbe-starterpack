<?php

namespace core\listener\events;

use core\caveblock\CaveManager;
use core\fakeinventory\inventory\EnchantInventory;
use core\fakeinventory\inventory\upgrader\BlackSmithInventory;
use core\form\forms\privatechest\LockForm;
use core\form\forms\privatechest\ManageChestForm;
use core\item\items\custom\TerrainAxe;
use core\listener\BaseListener;
use core\Main;
use core\manager\managers\privatechest\ChestManager;
use core\manager\managers\terrain\TerrainManager;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\block\Block;
use pocketmine\entity\projectile\Throwable;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Consumable;
use pocketmine\item\Item;
use pocketmine\scheduler\ClosureTask;
use pocketmine\tile\Chest;

class InteractListener extends BaseListener{

    private array $interact = [];
    private array $interactCooldowns = [];

    /**
     * @param PlayerInteractEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function blockInventories(PlayerInteractEvent $e) : void {

        if($e->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK)
            return;

        $block = $e->getBlock();
        $player = $e->getPlayer();

        if(CaveManager::isInCave($player)){
            $cave = CaveManager::getCave($player);
            if(!$cave->isMember($player->getName()))
                return;
        }

        if($block->getId() === Block::ANVIL && !$player->isSneaking()) {
            $e->setCancelled(true);
            (new BlackSmithInventory($player))->openFor([$player]);
            return;
        }

        if($block->getId() === Block::ENCHANTING_TABLE && !$player->isSneaking()) {
            $e->setCancelled(true);
            $level = $player->getLevel();
            $count = 0;
            $bx = $block->getX();
            $by = $block->getY();
            $bz = $block->getZ();

            for($i = 0; $i <= 2; $i++) {
                for($ii = 0; $ii <= 2; $ii++) {
                    if ($i === 0) {
                        if ($level->getBlockIdAt($bx, $by + $ii, $bz + 2) === Block::BOOKSHELF) {
                            $count++;
                        }
                    } else {
                        if ($level->getBlockIdAt($bx + $i, $by + $ii, $bz + 2) === Block::BOOKSHELF) {
                            $count++;
                        }
                        if ($level->getBlockIdAt($bx - $i, $by + $ii, $bz + 2) === Block::BOOKSHELF) {
                            $count++;
                        }
                    }
                }
            }

            for($i = 0; $i <= 2; $i++) {
                for($ii = 0; $ii <= 2; $ii++) {
                    if ($i === 0) {
                        if ($level->getBlockIdAt($bx, $by + $ii, $bz - 2) === Block::BOOKSHELF) {
                            $count++;
                        }
                    } else {
                        if ($level->getBlockIdAt($bx + $i, $by + $ii, $bz - 2) === Block::BOOKSHELF) {
                            $count++;
                        }
                        if ($level->getBlockIdAt($bx - $i, $by + $ii, $bz - 2) === Block::BOOKSHELF) {
                            $count++;
                        }
                    }
                }
            }

            for($i = 0; $i <= 1; $i++) {
                for($ii = 0; $ii <= 2; $ii++) {
                    if ($i === 0) {
                        if ($level->getBlockIdAt($bx + 2, $by + $ii, $bz) === Block::BOOKSHELF) {
                            $count++;
                        }
                    } else {
                        if ($level->getBlockIdAt($bx + 2, $by + $ii, $bz + $i) === Block::BOOKSHELF) {
                            $count++;
                        }
                        if ($level->getBlockIdAt($bx + 2, $by + $ii, $bz - $i) === Block::BOOKSHELF) {
                            $count++;
                        }
                    }
                }
            }

            for($i = 0; $i <= 1; $i++) {
                for($ii = 0; $ii <= 2; $ii++) {
                    if ($i === 0) {
                        if ($level->getBlockIdAt($bx - 2, $by + $ii, $bz) === Block::BOOKSHELF) {
                            $count++;
                        }
                    } else {
                        if ($level->getBlockIdAt($bx - 2, $by + $ii, $bz + $i) === Block::BOOKSHELF) {
                            $count++;
                        }
                        if ($level->getBlockIdAt($bx - 2, $by + $ii, $bz - $i) === Block::BOOKSHELF) {
                            $count++;
                        }
                    }
                }
            }

            (new EnchantInventory($player, $count))->openFor([$player]);
        }
    }

    public function onInteractEvent(PlayerInteractEvent $e) {

        if($e->getAction() === PlayerInteractEvent::RIGHT_CLICK_AIR)
            return;

        $player = $e->getPlayer();
        $block = $e->getBlock();

        if($player->isOp() || $player->hasPermission(ConfigUtil::PERMISSION_TAG."admin.cave"))
            return;

        if(!CaveManager::isInCave($player))
            return;

        $cave = CaveManager::getCave($player);

        if($cave->isOwner($player->getName()))
            return;

        if(!$cave->isMember($player->getName())){
            $e->setCancelled(true);
            return;
        }

        if(!$cave->getPlayerSetting($player->getName(), "o_chest") && $block->getId() === Block::CHEST || !$cave->getPlayerSetting($player->getName(), "i_beacon") && $block->getId() === Block::BEACON) {
            $e->setCancelled(true);
            $player->sendMessage(MessageUtil::format("Nie mozesz tego zrobic poniewaz nie masz uprawnien"));
            return;
        }

        if(!$cave->getCaveSetting("i_b_time"))
            return;

        if(date("H") >= ($from = $cave->getTimeSetting("f_time")) && date("H") <= ($to = $cave->getTimeSetting("t_time"))) {
            $e->setCancelled(true);
            if(!in_array($player->getName(), $this->interact)){

                $player->sendMessage(MessageUtil::format("Nie mozesz tego zrobic poniewaz wlasciel zablokowal interakcje od godziny §9" . $from . "§7 do godziny §9" . $to));
                $this->interact[] = $player->getName();

                Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player) : void{
                    if (($key = array_search($player->getName(), $this->interact)) !== false)
                        unset($this->interact[$key]);
                }), 20*5);
            }
            return;
        }
    }

    /**
     * @param PlayerInteractEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function ChestInteract(PlayerInteractEvent $e) : void{

        if($e->isCancelled())
            return;

        if($e->getAction() !== PlayerInteractEvent::RIGHT_CLICK_BLOCK)
            return;

        $block = $e->getBlock();

        if($block->getId() !== Block::CHEST)
            return;

        $player = $e->getPlayer();

        if(!CaveManager::isInCave($player))
            return;

        if($e->getItem()->getId() === Item::HOPPER)
            return;

        $tile = $block->getLevel()->getTile($block->asVector3());

        if(!$tile instanceof Chest)
            return;

        $blocks = [];

        $blocks[] = $block;

        if($tile->isPaired())
            $blocks[] = $tile->getPair();

        if(ChestManager::isLocked($block->asPosition())) {

            if(!$player->hasPermission(ConfigUtil::PERMISSION_TAG."privatechest")) {
                if(ChestManager::getChest($block->asPosition())->getOwner() !== $player->getName() && CaveManager::getCave($player)->getOwner() !== $player->getName()) {
                    $player->sendMessage(MessageUtil::format("Ta skrzynka jest zablokowana"));
                    $e->setCancelled(true);
                    return;
                }
            }

            if($player->isSneaking()) {
                $player->sendForm(new ManageChestForm($blocks));
                $e->setCancelled(true);
            }

            return;
        }

        if($player->isSneaking()) {

            if(ChestManager::getPlayerChestCount($player->getName()) >= ($limit = ChestManager::getMaxLockedChests($player))) {
                $player->sendMessage(MessageUtil::format("Osiagnales limit zablokowanych skrzynek, twoj limit wynosi §l§9" . $limit . "§r§7!"));
                $e->setCancelled(true);
                return;
            }

            $player->sendForm(new LockForm($blocks));
            $e->setCancelled(true);
        }
    }

    public function setPos(PlayerInteractEvent $e) : void {

        if($e->getAction() !== $e::RIGHT_CLICK_BLOCK)
            return;

        $player = $e->getPlayer();
        $item = $e->getItem();

        if (isset($this->interactCooldowns[$player->getName()]) && $this->interactCooldowns[$player->getName()] + 0.5 > microtime(true))
            return;

        $this->interactCooldowns[$player->getName()] = microtime(true);

        if($item->equalsExact(new TerrainAxe())) {
            $player->sendMessage(MessageUtil::format("Poprawnie zaznaczono druga pozycje!"));
            UserManager::getUser($player->getName())->setPos2($e->getBlock()->asPosition());
        }
    }

    /**
     * @param PlayerInteractEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function protectInteract(PlayerInteractEvent $e) : void {
        $block = $e->getBlock();

        if($e->getPlayer()->isOp())
            return;

        if(($e->getItem() instanceof Throwable || $e->getItem() instanceof Consumable) && $e->getAction() === $e::RIGHT_CLICK_AIR)
            return;

        if(($terrain = TerrainManager::getPriorityTerrain($block->asPosition())) !== null){
            if(!$terrain->isSettingEnabled("interact"))
                $e->setCancelled(true);
        }
    }
}