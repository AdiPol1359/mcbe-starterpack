<?php

declare(strict_types=1);

namespace core\tasks\sync;

use core\Main;
use core\utils\BroadcastUtil;
use core\utils\MessageUtil;
use core\utils\SoundUtil;
use core\utils\TimeUtil;
use pocketmine\scheduler\Task;

class NotifyTask extends Task {

    public function onRun() : void {

        BroadcastUtil::broadcastCallback(function($onlinePlayer) : void {
            $user = Main::getInstance()->getUserManager()->getUser($onlinePlayer->getName());

            if($user) {
                if($user->isVanished())
                    $onlinePlayer->sendTip("§7VANISH");

                if($user->isSafe()) {
                    $t = $user->getSafeTime() - time() - 1;
                    if($t < 0)
                        $t = 0;

                    $onlinePlayer->sendTip("§l§eOCHRONA §r§8(" . TimeUtil::convertIntToStringTime($t, "§e", "§7", true, false) . "§8)");
                }

                if($user->getSafeTime() <= time() + 10 && $user->getSafeTime() >= time() + 1)
                    SoundUtil::addSound([$onlinePlayer], $onlinePlayer->asPosition(), "random.pop", 100, 5);

                if($user->getSafeTime() === time()) {
                    $onlinePlayer->sendMessage(MessageUtil::formatLines(["Twoja ochrona skonczyla sie"], "OCHRONA"));
                    $onlinePlayer->sendTip("§l§eKONIEC OCHRONY");
                    SoundUtil::addSound([$onlinePlayer], $onlinePlayer->asVector3(), "block.false_permissions");
                }
            }

            if(($turboDrop = Main::getInstance()->getTurboDropManager()->getTurboDropFor($onlinePlayer->getName()))) {
                $onlinePlayer->sendTip("§l§e".($turboDrop->isServer() ? "§eSERWEROWY " : "")."TURBODROP §r§e" . TimeUtil::convertIntToStringTime($turboDrop->getExpireTime() - time(), "§e", "§7", true, false));
            }
        });
    }
}