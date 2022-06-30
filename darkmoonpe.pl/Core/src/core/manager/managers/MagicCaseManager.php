<?php

namespace core\manager\managers;

use core\fakeinventory\inventory\MagicCaseInventory;
use core\Main;
use core\manager\BaseManager;
use core\task\tasks\DrawTask;
use core\util\utils\ItemUtil;
use core\util\utils\RandomUtil;
use pocketmine\item\Item;
use pocketmine\Player;

class MagicCaseManager extends BaseManager {

    private static array $players;

    public static function getRandomItem() : Item {

        $config = Main::getMagicCase();
        $items = [];

        foreach($config->get("items") as $data => $itemData)
            $items[$itemData['item']] = (float)$itemData['chance'];

        $randomItem = RandomUtil::randomDraw($items);

        if($randomItem === "" || !$randomItem)
            return self::getRandomItem();

        return ItemUtil::getItemFromConfig($randomItem);
    }

    public static function openingMagicCase(Player $player) : bool {
        return isset(self::$players[$player->getName()]);
    }

    public static function setOpeningMagicCase(Player $player, MagicCaseInventory $inventory) : void {
        self::$players[$player->getName()] = $inventory;
    }

    public static function removeOpeningMagicCase(Player $player) : void {
        unset(self::$players[$player->getName()]);
    }

    public static function getMagicCaseInventory(Player $player) : ?MagicCaseInventory {
        return self::$players[$player->getName()] ?? null;
    }

    public static function openMagicCase(Player $player) : void {

        $inventory = new MagicCaseInventory($player);

        self::setOpeningMagicCase($player, $inventory);

        $task = new DrawTask($inventory);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask($task, 5);
        $inventory->setTask($task);
        $inventory->openFor([$player]);
    }
}