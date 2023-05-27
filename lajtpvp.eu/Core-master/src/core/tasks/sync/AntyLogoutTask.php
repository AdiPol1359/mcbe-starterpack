<?php

declare(strict_types=1);

namespace core\tasks\sync;

use core\Main;
use core\utils\MessageUtil;
use core\utils\SoundUtil;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class AntyLogoutTask extends Task {

    public function onRun() : void {
        foreach(Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            $user = Main::getInstance()->getUserManager()->getUser($onlinePlayer->getName());

            if(!$user)
                continue;

            if($user->hasAntyLogout() && ($user->getAntyLogoutTime()) <= time()) {
                $user->resetAntyLogout();
                continue;
            }

            if(($time = $user->getAntyLogoutTime()) === time()) {
                $user->resetAntyLogout();

                SoundUtil::addSound([$onlinePlayer], $onlinePlayer->getPosition(), "random.explode");
                $onlinePlayer->sendMessage(MessageUtil::format("Walka zakonczona!"));
                $onlinePlayer->sendTip("§l§aAntyLogout");
                continue;
            }

            if($time <= time())
                continue;

            $t = $time - 1;
            if($t < 0)
                $t = 0;

            $onlinePlayer->sendTip("§7AntyLogout: §e" . ($t - time()));
        }
    }
}