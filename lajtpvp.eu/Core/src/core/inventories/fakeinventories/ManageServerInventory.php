<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories;

use core\inventories\FakeInventory;
use core\inventories\FakeInventoryPatterns;
use core\inventories\FakeInventorySize;
use core\Main;
use core\managers\ServerManager;
use core\utils\ItemUtil;
use pocketmine\item\Item;
use pocketmine\player\Player;

class ManageServerInventory extends FakeInventory {

    public function __construct() {
        parent::__construct("§l§eUSTAWIENIA SERWERA", FakeInventorySize::LARGE_CHEST);
    }

    public function setItems() : void{
        $this->fillWithPattern(FakeInventoryPatterns::PATTERN_FILL_UP_AND_DOWN_WITH_ROWS);

        foreach(Main::getInstance()->getServerManager()->getSettings() as $setting => $settingData) {
            if(!isset($settingData["item"]) || !isset($settingData["slot"]) || !isset($settingData["name"]))
                continue;

            $item = clone $settingData["item"];
            if(Main::getInstance()->getServerManager()->isSettingEnabled($setting))
                ItemUtil::addItemGlow($item);
            $item->setCustomName("§l".(Main::getInstance()->getServerManager()->isSettingEnabled($setting) ? "§a" : "§c").strtoupper($settingData["name"]));
            $item->getNamedTag()->setString("setting", $setting);
            $this->setItem($settingData["slot"], $item, true, true);
        }
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        $namedTag = $sourceItem->getNamedTag();

        if($namedTag->getTag("setting")) {
            $setting = $namedTag->getString("setting");

            Main::getInstance()->getServerManager()->setSetting($setting, !Main::getInstance()->getServerManager()->isSettingEnabled($setting));

            if($setting !== ServerManager::TNT)
                Main::getInstance()->getServerManager()->notify($setting);

            $this->setItems();
        }

        $this->unClickItem($player);
        return true;
    }
}