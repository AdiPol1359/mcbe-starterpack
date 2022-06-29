<?php

namespace core\manager\managers;

use core\fakeinventory\inventory\upgrader\UpgraderDrawInventory;
use core\Main;
use core\manager\BaseManager;
use core\task\tasks\DrawTask;
use pocketmine\item\Item;
use pocketmine\Player;

class UpgradeManager extends BaseManager {

    private static array $players;

    public static function openingUpgradeDraw(Player $player) : bool {
        return isset(self::$players[$player->getName()]);
    }

    public static function setOpeningUpgradeDraw(Player $player, UpgraderDrawInventory $inventory) : void {
        self::$players[$player->getName()] = $inventory;
    }

    public static function removeOpeningUpgradeDraw(Player $player) : void {
        unset(self::$players[$player->getName()]);
    }

    public static function getUpgradeDrawInventory(Player $player) : ?UpgraderDrawInventory {
        return self::$players[$player->getName()] ?? null;
    }

    public static function openUpgradeDraw(Player $player, Item $item) : void {

        $inventory = new UpgraderDrawInventory($player, $item);

        self::setOpeningUpgradeDraw($player, $inventory);

        $task = new DrawTask($inventory);
        Main::getInstance()->getScheduler()->scheduleRepeatingTask($task, 5);
        $inventory->setTask($task);
        $inventory->openFor([$player]);
    }
}