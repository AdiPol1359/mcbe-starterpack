<?php

declare(strict_types=1);

namespace core\listeners\player;

use core\inventories\fakeinventories\guild\ItemsInventory;
use core\inventories\FakeInventoryManager;
use core\items\custom\FastPickaxe;
use core\Main;
use core\utils\MessageUtil;
use core\utils\Settings;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDropItemEvent;

class PlayerDropItemListener implements Listener {

    public function refreshInventory(PlayerDropItemEvent $e) : void {
        $player = $e->getPlayer();

        if(!FakeInventoryManager::isOpening($player->getName()))
            return;

        $inventory = FakeInventoryManager::getInventory($player->getName());

        if(!$inventory instanceof ItemsInventory)
            return;

        $inventory->setItems();
    }

    public function lobbyDropItem(PlayerDropItemEvent $e) : void {
        $player = $e->getPlayer();

        if($player->getWorld()->getDisplayName() === Settings::$LOBBY_WORLD) {
            $e->cancel();
        }
    }

    public function blockDropFastPickaxe(PlayerDropItemEvent $e) : void {
        $player = $e->getPlayer();
        $item = $e->getItem();
        $user = Main::getInstance()->getUserManager()->getUser($player->getName());

        if(!$user) {
            return;
        }

        if($item->equals((new FastPickaxe())->__toItem(), false)) {
            if(!$user->hasLastData(Settings::$DROP_FASTPICKAXE)) {
                $user->setLastData(Settings::$DROP_FASTPICKAXE, (time() + Settings::$DROP_FASTPICKAXE_TIME), Settings::$TIME_TYPE);
                $player->sendMessage(MessageUtil::format("Nie mozesz wyrzucic §ekilofa §e6§8/§e3§8/§e3"));
            }

            $e->cancel();
        }
    }
}