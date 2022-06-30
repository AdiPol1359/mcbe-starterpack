<?php

namespace core\task\tasks;

use core\manager\managers\BanManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class PunishmentMessageTask extends Task {
    public function onRun(int $currentTick) {
        foreach(Server::getInstance()->getLevelByName(ConfigUtil::LOBBY_WORLD)->getPlayers() as $p) {
            if(BanManager::isBanned($p->getName()))
                $p->sendMessage(MessageUtil::customFormat(BanManager::getBannedMessage($p), "§l§9JESTES ZBANOWANY!"));
        }
    }
}