<?php

namespace core\listener\events;

use core\caveblock\CaveManager;
use core\item\items\custom\Cobblex;
use core\item\items\custom\MagicCase;
use core\listener\BaseListener;
use core\manager\managers\LogManager;
use core\manager\managers\MagicCaseManager;
use core\manager\managers\ParticlesManager;
use core\manager\managers\terrain\TerrainManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\block\Block;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\item\Item;

class BlockPlaceListener extends BaseListener{

    public function BlockPlaceInCave(BlockPlaceEvent $e) {

        $player = $e->getPlayer();
        $block = $e->getBlock();

        if($player->isOp() || $player->hasPermission(ConfigUtil::PERMISSION_TAG."admin.cave"))
            return;

        if(!CaveManager::isInCave($player))
            return;

        $cave = CaveManager::getCave($player);

        if($cave->isOwner($player->getName()))
            return;

        if(!$cave->isMember($player->getName())) {
            $player->sendMessage(MessageUtil::format("Nie mozesz tego zrobic poniewaz nie jestes czlonkiem tej jaskini!"));
            $e->setCancelled(true);
            return;
        }

        if($cave->getCaveSetting("b_n_pl")) {
            $e->setCancelled(true);
            $player->sendMessage(MessageUtil::format("Nie mozesz tego zrobic poniewaz wlasciciel wylaczyl niszczenie dla wszystkich graczy!"));
            return;
        }

        if(!$cave->getPlayerSetting($player->getName(), "p_block") || $block->getId() === Block::BEACON && !$cave->getPlayerSetting($player->getName(), "i_beacon")) {
            $e->setCancelled(true);
            $player->sendMessage(MessageUtil::format("Nie mozesz tego zrobic poniewaz nie masz uprawnien!"));
            return;
        }

        if($cave->getCaveSetting("b_n_off") && !$cave->isOnlineOwner()) {
            $e->setCancelled(true);
            $player->sendMessage(MessageUtil::format("Nie mozesz tego zrobic poniewaz wlasciciela jaskini nie ma na serwerze!"));
            return;
        }

        if(!$cave->getCaveSetting("b_n_time"))
            return;

        if(date("H") >= ($from = $cave->getTimeSetting("f_time")) && date("H") <= ($to = $cave->getTimeSetting("t_time"))) {
            $e->setCancelled(true);
            $player->sendMessage(MessageUtil::format("Nie mozesz tego zrobic poniewaz wlasciel zablokowal budowanie od godziny §9" . $from . "§7 do godziny §9" . $to));
            return;
        }
    }

    /**
     * @param BlockPlaceEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function MagicCase(BlockPlaceEvent $e) : void {

        $player = $e->getPlayer();
        $item = $player->getInventory()->getItemInHand();

        if(!$item->equals(new MagicCase()))
            return;

        if($e->isCancelled())
            return;

        $e->setCancelled(true);

        if(!MagicCaseManager::openingMagicCase($player)) {
            $item->pop();
            $player->getInventory()->setItemInHand($item);

            MagicCaseManager::openMagicCase($player);
            LogManager::sendLog($player, "StartOpening", LogManager::MAGIC_CASE);

            return;
        }

        MagicCaseManager::getMagicCaseInventory($player)->openFor([$player]);

    }

    /*public function PlaceExcavator(BlockPlaceEvent $e) : void{

        if($e->isCancelled())
            return;

        $item = $e->getItem();
        $block = $e->getBlock();

        if($item->getId() === Item::STONE){
            $koparka = Item::get(Item::CAULDRON);
            $koparka->setCustomName("§l§9KOPARKA\n§r§8(§7Postaw aby zrespic koparke§8)\n§9§lUWAGA §r§7Koparka zniszczy wszystkie bloki w odleglosci 4 blokow!");
            $koparka->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::UNBREAKING), 10));
            $e->getPlayer()->getInventory()->addItem($koparka);

        }

        if($item->getId() === Item::CAULDRON) {
            if($item->getCustomName() === "§l§9KOPARKA\n§r§8(§7Postaw aby zrespic koparke§8)\n§9§lUWAGA §r§7Koparka zniszczy wszystkie bloki w odleglosci 4 blokow!" && $item->hasEnchantment(Enchantment::UNBREAKING, 10))
                ShapeUtils::createExcavator($block->asPosition());
        }
    }*/

    public function CaveBorder(BlockPlaceEvent $e) {

        if(!CaveManager::isInCave($e->getPlayer()))
            return;

        $block = $e->getBlock();
        $x = $block->getFloorX();
        $z = $block->getFloorZ();

        $border = ConfigUtil::CAVE_BORDER;

        if($x >= $border || $x <= -$border || $z >= $border || $z <= -$border)
            $e->setCancelled(true);
    }

    public function BlockPlaceEventBorder(BlockPlaceEvent $e) : void {
        $player = $e->getPlayer();
        $block = $e->getBlock();
        if(CaveManager::isInCave($player)) {
            if($block->y >= 100)
                $e->setCancelled(true);
        }
    }

    /**
     * @param BlockPlaceEvent $e
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function CobbleX(BlockPlaceEvent $e) {
        $player = $e->getPlayer();

        $itemInHand = $e->getItem();
        $block = $e->getBlock();

        if(!$itemInHand->equals(new Cobblex()))
            return;

        if($e->isCancelled())
            return;

        $itemInHand->setCount(1);
        $player->getInventory()->removeItem($itemInHand);

        $pos = $block->asVector3();

        $e->setCancelled(true);

        $item = null;

        $bookshelf = round(rand(0, 10000) / 100, 2) < 5;
        $apple = round(rand(0, 10000) / 100, 2) < 30;
        $gold = round(rand(0, 10000) / 100, 2) < 20;
        $emerald = round(rand(0, 10000) / 100, 2) < 20;
        $iron = round(rand(0, 10000) / 100, 2) < 20;
        $case = round(rand(0, 10000) / 100, 2) < 3;
        $enchant = round(rand(0, 10000) / 100, 2) < 5;
        $kowadlo = round(rand(0, 10000) / 100, 2) < 5;

        switch(true) {

            case $case:

                $item = new MagicCase();

                $player->sendMessage(MessageUtil::format("Wylosowales §9PremiumCase§7! §8(§7x§l§91§r§8)"));

                break;

            case $bookshelf:

                $item = Item::get(Item::BOOKSHELF, 0, 6);

                $player->sendMessage(MessageUtil::format("Wylosowales §l§9Biblioteczki§r§7! §8(§7x§l§96§r§8)"));

                break;
            case $apple:

                $item = Item::get(Item::APPLE, 0, 4);

                $player->sendMessage(MessageUtil::format("Wylosowales §l§9Jablka§r§7! §8(§7x§l§94§r§8)"));

                break;
            case $gold:

                $item = Item::get(Item::GOLD_INGOT, 0, 14);

                $player->sendMessage(MessageUtil::format("Wylosowales §l§9Zloto§r§7! §8(§7x§l§914§r§8)"));

                break;
            case $emerald:

                $item = Item::get(Item::EMERALD, 0, 14);

                $player->sendMessage(MessageUtil::format("Wylosowales §l§9Emerald§r§7! §8(§7x§l§914§r§8)"));

                break;
            case $iron:

                $item = Item::get(Item::IRON_INGOT, 0, 14);

                $player->sendMessage(MessageUtil::format("Wylosowales §l§9Zelazo§r§7! §8(§7x§l§914§r§8)"));

                break;
            case $enchant:

                $item = Item::get(Item::ENCHANTING_TABLE);

                $player->sendMessage(MessageUtil::format("Wylosowales §l§9Enchant Table§r§7! §8(§7x§l§91§r§8)"));

                break;
            case $kowadlo:

                $item = Item::get(Item::ANVIL);

                $player->sendMessage(MessageUtil::format("Wylosowales §l§9Kowadlo§r§7! §8(§7x§l§91§r§8)"));

                break;
        }

        if(!$item) {
            $item = Item::get(Item::DIAMOND, 0, 14);

            $player->sendMessage(MessageUtil::format("Wylosowales §9§lDiamenty§r§7! §8(§7x§l§914§r§8)"));
        }

        $player->getLevel()->dropItem($pos, $item);

        ParticlesManager::spawnFirework($player, $player->getLevel(), [[ParticlesManager::TYPE_STAR, ParticlesManager::COLOR_YELLOW], [ParticlesManager::TYPE_STAR, ParticlesManager::COLOR_GOLD]]);
    }

    /**
     * @param BlockPlaceEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function protectBlockPlace(BlockPlaceEvent $e) : void {
        $block = $e->getBlock();

        if($e->getPlayer()->isOp())
            return;

        if(($terrain = TerrainManager::getPriorityTerrain($block)) !== null){
            if(!$terrain->isSettingEnabled("block_place")) {
                $e->setCancelled(true);
                $e->getPlayer()->sendTip("§cStawianie blokow na tym terenie jest zablokowane!");
            }
        }
    }
}