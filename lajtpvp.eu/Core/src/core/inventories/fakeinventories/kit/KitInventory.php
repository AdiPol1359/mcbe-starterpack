<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories\kit;

use core\inventories\FakeInventory;
use core\inventories\FakeInventoryPatterns;
use core\inventories\FakeInventorySize;
use core\Main;
use core\utils\PermissionUtil;
use core\utils\Settings;
use core\utils\TimeUtil;
use pocketmine\item\Item;
use pocketmine\player\Player;

class KitInventory extends FakeInventory {

    public function __construct(private Player $player) {
        parent::__construct("§l§eKIT", FakeInventorySize::LARGE_CHEST);
    }

    public function setItems() : void {
        $this->fillWithPattern(FakeInventoryPatterns::PATTERN_FILL_CORNERS);

        $user = Main::getInstance()->getUserManager()->getUser($this->player->getName());
        $kitManager = $user->getKitManager();

        foreach(Settings::$KITS as $kitName => $kitData) {
            $item = clone $kitData["inventoryItem"];
            $item->setCustomName("§7Kit " . strtolower($kitName));

            if(($permission = $kitData["permission"]) !== null && !PermissionUtil::has($this->player, $permission))
                $item->setCustomName($item->getCustomName() . " §cNIEDOSTEPNY");
            else {
                if($user) {
                    if($kitManager->canUseKit($kitName))
                        $item->setCustomName($item->getCustomName() . " §aDOSTEPNY");
                    else
                        $item->setCustomName($item->getCustomName() . " §eDOSTEPNY ZA §8(§e".TimeUtil::convertIntToStringTime(($kitManager->getUseKitTime($kitName) - time()), "§e", "§7", true, false)."§8)");
                }
            }

            $item->getNamedTag()->setString("kitName", $kitName);

            $this->setItem($kitData["slot"], $item, true, true);
        }
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        $namedTag = $sourceItem->getNamedTag();

        if($namedTag->getTag("kitName")) {
            $kitName = $namedTag->getString("kitName");
            $this->changeInventory($player, (new KitItemsInventory($player, $kitName)));
        }

        $this->unClickItem($player);
        return true;
    }
}