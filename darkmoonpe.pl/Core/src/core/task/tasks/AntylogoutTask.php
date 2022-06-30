<?php

namespace core\task\tasks;

use core\Main;
use core\util\utils\MessageUtil;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class AntylogoutTask extends Task {

    public function onRun(int $currentTick) {

        foreach(Main::$antylogout as $nick => $data) {

            $player = Server::getInstance()->getPlayerExact($nick);
            if(!$player)
                continue;

            if($data["time"] <= time()) {
                $player->sendMessage(MessageUtil::format("Koniec antylogouta!"));
                unset(Main::$antylogout[$nick]);
                return;
            }

            $player->sendTip("§7ANTYLOGOUT §l§8(§9".($data["time"] - time())."§8)");
        }
    }
}