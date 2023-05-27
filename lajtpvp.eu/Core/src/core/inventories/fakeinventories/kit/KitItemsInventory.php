<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories\kit;

use core\inventories\FakeInventory;
use core\inventories\FakeInventoryPatterns;
use core\inventories\FakeInventorySize;
use core\Main;
use core\utils\PermissionUtil;
use core\utils\Settings;
use core\utils\InventoryUtil;
use core\utils\TimeUtil;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class KitItemsInventory extends FakeInventory {

    public function __construct(private Player $player, private string $kitName) {
        parent::__construct("§l§eKIT", FakeInventorySize::LARGE_CHEST);
    }

    public function setItems() : void {
        $this->fillWithPattern(FakeInventoryPatterns::PATTERN_FILL_UP_AND_DOWN);

        foreach(Settings::$KITS[$this->kitName]["items"] as $item)
            $this->addItem(clone $item);

        $user = Main::getInstance()->getUserManager()->getUser($this->player->getName());
        $kitManager = $user->getKitManager();

        $name = "§cNIEDOSTEPNY";
        $meta = 14;

        if(($permission = Settings::$KITS[$this->kitName]["permission"]) !== null && !PermissionUtil::has($this->player, $permission)) {
            $name = "§cNIEDOSTEPNY";
            $meta = 14;
        }else {
            if($user) {
                if($kitManager->canUseKit($this->kitName)) {
                    $name = "§aDOSTEPNY";
                    $meta = 5;
                } else {
                    $name = "§eDOSTEPNY ZA §8(§e" . TimeUtil::convertIntToStringTime(($kitManager->getUseKitTime($this->kitName) - time()), "§e", "§7", true, false) . "§8)";
                    $meta = 1;
                }
            }
        }

        $this->setItem(49, ItemFactory::getInstance()->get(ItemIds::CONCRETE, $meta)->setCustomName($name), true, true);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        $user = Main::getInstance()->getUserManager()->getUser($player->getName());
        $kitManager = $user->getKitManager();

        if($sourceItem->getId() === ItemIds::CONCRETE) {
            if(($permission = Settings::$KITS[$this->kitName]["permission"]) === null || PermissionUtil::has($this->player, $permission)) {
                if($user) {
                    if($kitManager->canUseKit($this->kitName) || $player->getServer()->isOp($player->getName()) || PermissionUtil::has($player, Settings::$PERMISSION_TAG."kit.all")) {
                        foreach(Settings::$KITS[$this->kitName]["items"] as $item)
                            InventoryUtil::addItem(clone $item, $player);

                        $kitManager->setKit($this->kitName, (time() + Settings::$KITS[$this->kitName]["time"]));

                        $this->closeFor($player);
                    }
                }
            }
        }

        $this->unClickItem($player);
        return true;
    }
}