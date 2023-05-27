<?php

declare(strict_types=1);

namespace core\tasks\sync;

use core\Main;
use core\utils\BroadcastUtil;
use core\utils\MessageUtil;
use pocketmine\scheduler\Task;

class GuildTask extends Task {

    public function onRun() : void {

        foreach(Main::getInstance()->getGuildManager()->getGuilds() as $key => $guild) {
            if($guild->getExpireTime() <= time()) {
                BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($guild) : void {
                    $onlinePlayer->sendMessage(MessageUtil::format("Gildia §e". $guild->getTag()." §8[§e".$guild->getName()."§8] §7Wygasla! §eX§7/§eZ §8(§e".$guild->getHeartSpawn()->x."§7/§e".$guild->getHeartSpawn()->z."§8)"));
                });

                if(($war = Main::getInstance()->getWarManager()->getWar($guild->getTag())) !== null)
                    $war->endWar(($war->getAttacker() === $guild->getTag() ? $war->getAttacked() : $war->getAttacker()), true);

                Main::getInstance()->getGuildManager()->deleteGuild($guild->getTag());
            }
        }
    }
}