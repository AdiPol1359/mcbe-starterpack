<?php

namespace core\inventories\fakeinventories;

use core\inventories\FakeInventory;
use core\Main;
use core\managers\TeleportManager;
use core\utils\MessageUtil;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class WarpInventory extends FakeInventory {

    public function __construct() {
        parent::__construct("§l§eWARP");
    }

    public function setItems() : void {
        $this->fill();
        $itemFactory = ItemFactory::getInstance();

        $lastSlot = 0;

        foreach(Main::getInstance()->getWarpManager()->getWarps() as $name => $position) {
            $warpName = base64_decode($name);

            $item = $itemFactory->get(ItemIds::BUCKET, 8)->setCustomName("§l§e".$warpName);
            $item->getNamedTag()->setString("warp", $warpName);

            $this->setItem($lastSlot, $item, true, true);
            $lastSlot++;
        }
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        $namedTag = $sourceItem->getNamedTag();

        if($namedTag->getTag("warp")) {
            $warpName = $namedTag->getString("warp");

            if(!($warp = Main::getInstance()->getWarpManager()->getWarp($warpName))) {
                $this->onClose($player);
                $this->unClickItem($player);
                return true;
            }

            $position = $warp->getPosition();

            if(TeleportManager::isTeleporting($player->getName())) {
                $this->onClose($player);
                $player->sendMessage(MessageUtil::format("Jestes w trakcje teleportacji!"));
                $this->unClickItem($player);
                return true;
            }

            $this->onClose($player);

            TeleportManager::teleport($player, $position);
        }

        $this->unClickItem($player);
        return true;
    }
}