<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories;

use core\inventories\FakeInventory;
use core\inventories\FakeInventorySize;
use core\Main;
use core\utils\BroadcastUtil;
use core\utils\MessageUtil;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class ServiceInventory extends FakeInventory {

    public function __construct(private Player $player) {
        parent::__construct("§l§eSERVICES", FakeInventorySize::LARGE_CHEST);
    }

    public function setItems() : void {

        $itemFactory = ItemFactory::getInstance();
        $this->clearAll();
        $user = Main::getInstance()->getUserManager()->getUser($this->player->getName());

        if(!$user)
            return;

        $lastSlot = 0;

        foreach($user->getServicesManager()->getServicesToCollect() as $service) {
            $item = $itemFactory->get(ItemIds::ENCHANTED_BOOK);
            $item->setCustomName("§e".Main::getInstance()->getServicesManager()->getService($service["service"])->getName()." §8(§7kliknij aby oderbac§8)");
            $item->getNamedTag()->setInt("serviceId", $service["id"]);
            $item->getNamedTag()->setInt("serviceNameId", $service["service"]);

            $this->setItem($lastSlot, $item, true, true);
            $lastSlot++;
        }
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        $namedTag = $sourceItem->getNamedTag();
        $user = Main::getInstance()->getUserManager()->getUser($player->getName());
        $servicesManager = $user->getServicesManager();

        if($namedTag->getTag("serviceId")) {
            $id = $namedTag->getInt("serviceId");
            $serviceName = $namedTag->getInt("serviceNameId");

            if(($service = Main::getInstance()->getServicesManager()->getService($serviceName)) !== null) {
                if(!$servicesManager->isCollected($id)) {
                    $servicesManager->claimReward($id, $service->getCommand());
                    $this->setItems();

                    BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($service, $player) : void {
                        $onlinePlayer->sendMessage(MessageUtil::formatLines([
                            "Gracz §e" . $player->getName() . " §7zakupil §e" . $service->getName(),
                            "Zakupu dokonal na stronie §eLajtPVP.PL§7!"
                        ], "ITEMSHOP"));
                    });
                }
            }
        }

        $this->unClickItem($player);
        return true;
    }
}