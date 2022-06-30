<?php

namespace core\task\tasks;

use core\manager\managers\BanManager;
use core\manager\managers\WhitelistManager;
use core\util\utils\ConfigUtil;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class LobbyTask extends Task {

    private int $lastSound = 0;

    private const TELEPORT_SPAWN = 1;
    private const TELEPORT_LOBBY = 2;

    public function onRun(int $currentTick) : void {
        $server = Server::getInstance();

        foreach($server->getOnlinePlayers() as $player) {

            $action = 0;

            if(WhitelistManager::isWhitelistEnabled()) {
                if($player->getLevel()->getName() === ConfigUtil::LOBBY_WORLD) {
                    if(WhitelistManager::isInWhitelist($player->getName()) || $player->isOp() || $player->hasPermission(ConfigUtil::PERMISSION_TAG . "whitelist"))
                        $action = self::TELEPORT_SPAWN;
                } else {
                    if(!WhitelistManager::isInWhitelist($player->getName()) && !$player->isOp() && !$player->hasPermission(ConfigUtil::PERMISSION_TAG . "whitelist"))
                        $action = self::TELEPORT_LOBBY;
                }
            }

            if(BanManager::isBanned($player->getName()) && $player->getLevel()->getName() !== ConfigUtil::LOBBY_WORLD && !$player->isOp())
                $action = self::TELEPORT_LOBBY;
            else {
                if($player->getLevel()->getName() === ConfigUtil::LOBBY_WORLD && !BanManager::isBanned($player->getName())) {
                    if(WhitelistManager::isWhitelistEnabled()) {
                        if(WhitelistManager::isInWhitelist($player->getName()) || $player->isOp() || $player->hasPermission(ConfigUtil::PERMISSION_TAG . "whitelist"))
                            $action = self::TELEPORT_SPAWN;
                    } else
                        $action = self::TELEPORT_SPAWN;
                }
            }

            switch($action) {
                case self::TELEPORT_LOBBY:
                    $player->teleport(Server::getInstance()->getLevelByName(ConfigUtil::LOBBY_WORLD)->getSafeSpawn());
                    break;

                case self::TELEPORT_SPAWN:
                    $player->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn());
                    break;
            }

            if(!WhitelistManager::isWhitelistEnabled())
                return;

            if($this->lastSound == 3) {
                $level = $server->getLevelByName(ConfigUtil::LOBBY_WORLD);
                $level->broadcastLevelSoundEvent($level->getSafeSpawn(), LevelSoundEventPacket::SOUND_PORTAL);
                $this->lastSound = 0;
            }
            $this->lastSound++;

            $level = $server->getLevelByName(ConfigUtil::LOBBY_WORLD);
            foreach($level->getPlayers() as $p)
                $p->addTitle("§l§9START ZA:", WhitelistManager::dateFormat(), 0, 30, 20);

            if(WhitelistManager::getWhitelistDate() != null && time() >= strtotime(WhitelistManager::getWhitelistDate())) {
                WhitelistManager::setWhitelist(false);

                foreach($server->getLevelByName(ConfigUtil::LOBBY_WORLD)->getPlayers() as $levelPlayer) {
                    $pos = Server::getInstance()->getDefaultLevel()->getSafeSpawn();
                    $pos->y += 3;
                    $levelPlayer->teleport($pos);
                    foreach($server->getOnlinePlayers() as $serverPlayer) {
                        if($levelPlayer === $serverPlayer)
                            continue;

                        $levelPlayer->showPlayer($serverPlayer);
                        $serverPlayer->showPlayer($levelPlayer);
                    }
                }
            }
        }
    }
}