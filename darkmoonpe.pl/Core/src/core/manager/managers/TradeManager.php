<?php

namespace core\manager\managers;

use core\fakeinventory\inventory\TradeInventory;
use core\Main;
use core\manager\BaseManager;
use pocketmine\Player;

class TradeManager extends BaseManager {

    public static function sendTrade(Player $sender, Player $player) : void {
        Main::$tradeRequests[$player->getName()] = $sender->getName();
    }

    public static function acceptTrade(Player $player) : void {
        $senderName = Main::$tradeRequests[$player->getName()];
        $sender = self::getServer()->getPlayer($senderName);
        unset(Main::$tradeRequests[$player->getName()]);
        if($sender) {
            $gui = (new TradeInventory($sender, $player));
            $gui->openFor([$sender, $player]);
        }
    }

    public static function checkTrade(Player $player) : bool {
        return isset(Main::$tradeRequests[$player->getName()]);
    }
}