<?php

declare(strict_types=1);

namespace core\listeners\player;

use core\Main;
use core\managers\BorderPlayerManager;
use core\managers\bossbar\BossbarManager;
use core\managers\bossbar\bossbars\GuildTerrain;
use core\managers\TeleportManager;
use core\tasks\sync\TeleportTask;
use core\utils\Settings;
use pocketmine\block\Air;
use pocketmine\block\Stair;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\player\GameMode;
use pocketmine\Server;

class PlayerMoveListener implements Listener {

    public array $isOnStair = [];
    public array $isInAir = [];

    public function borderInfo(PlayerMoveEvent $e) : void {

        if($e->getFrom()->floor()->equals($e->getTo()->floor()))
            return;

        $player = $e->getPlayer();
        $pos = $player->getPosition();

        $x = $pos->getFloorX();
        $z = $pos->getFloorZ();

        $border = Settings::$BORDER_DATA["border"];

        if(abs($x) >= ($border - 20)) {
            $distance = 20 - (abs($x) - ($border - 20));
            $player->sendPopup("§e" . $distance . "§r§7 kratek do borderu");
        }

        if(abs($z) >= ($border - 20)) {
            $distance = 20 - (abs($z) - ($border - 20));
            $player->sendPopup("§e" . $distance . "§r§7 kratek do borderu");
        }

        if(Settings::$BORDER_DATA["knock"]) {
            if($x >= Settings::$BORDER_DATA["border"])
                $player->knockBack(0, -2, 0, 0.5);

            if($x <= -Settings::$BORDER_DATA["border"])
                $player->knockBack(0, 2, 0, 0.5);

            if($z >= Settings::$BORDER_DATA["border"])
                $player->knockBack(0, 0, -2, 0.5);

            if($z <= -Settings::$BORDER_DATA["border"])
                $player->knockBack(0, 0, 2, 0.5);
        }

        if(abs($x) >= ($border + 20) || abs($z) >= ($border + 20) && Settings::$BORDER_DATA["damage"]) {
            if(!BorderPlayerManager::isInBorder($player->getName()))
                BorderPlayerManager::addPlayer($player);
        } else
            BorderPlayerManager::removePlayer($player->getName());
    }

    public function spawnKnock(PlayerMoveEvent $e) : void {
        $player = $e->getPlayer();
        $pos = $player->getPosition();

        $user = Main::getInstance()->getUserManager()->getUser($player->getName());

        if(!$user->hasAntyLogout())
            return;

        foreach($terrain = Main::getInstance()->getTerrainManager()->getTerrainsFromPos($player->getPosition()) as $terrain) {
            if($terrain->getName() === Settings::$SPAWN_TERRAIN) {
                $x = $pos->getFloorX() - $player->getWorld()->getSafeSpawn()->getFloorX();
                $z = $pos->getFloorZ() - $player->getWorld()->getSafeSpawn()->getFloorZ();

                $player->knockBack(0, $x, $z, 0.5);
            }
        }
    }

    public function stairsDamage(PlayerMoveEvent $e) : void {
        if($e->getFrom()->floor()->equals($e->getTo()))
            return;

        $player = $e->getPlayer();
        $pos = $player->getPosition();
        $nick = $player->getName();

        $lastPos = $e->getFrom();

        $blocks = [];

        $isOnStair = ($this->isOnStair[$nick] ?? false);

        $blocks[0] = $player->getWorld()->getBlock($pos->add(0, -1, 0));
        $blocks[1] = $player->getWorld()->getBlock($pos->add(0, -2, 0));
        $blocks[2] = $player->getWorld()->getBlock($lastPos->add(0, -1, 0));
        $blocks[3] = $player->getWorld()->getBlock($lastPos->add(0, -2, 0));

        if($isOnStair)
            $player->resetFallDistance();

        if($blocks[2] instanceof Air && $blocks[3] instanceof Air)
            $this->isOnStair[$nick] = true;
        else
            $this->isOnStair[$nick] = false;

        if($blocks[0] instanceof Stair || ($blocks[0] instanceof Air && $blocks[1] instanceof Stair) && !$this->isInAir[$nick])
            $this->isOnStair[$nick] = true;
        else
            $this->isOnStair[$nick] = false;
    }

    public function onMoveTeleport(PlayerMoveEvent $e) : void {
        if($e->getFrom()->floor()->equals($e->getTo()->floor()))
            return;

        $player = $e->getPlayer();

        if(!TeleportManager::isTeleporting($player->getName()))
            return;

        $task = TeleportManager::getTeleport($player->getName())->getTask();

        if($task instanceof TeleportTask)
            $task->stop();
    }

    public function lobbyMoveBlock(PlayerMoveEvent $e) {
        if($e->getPlayer()->getWorld()->getDisplayName() === Settings::$LOBBY_WORLD)
            $e->cancel();
    }

    public function guildTerrainBossbar(PlayerMoveEvent $e) : void {
        if($e->getFrom()->floor()->equals($e->getTo()->floor()))
            return;

        $player = $e->getPlayer();

        if(($guild = Main::getInstance()->getGuildManager()->getGuildFromPos($player->getPosition())) !== null) {
            $color = $guild->getColorForPlayer($player->getName());
            $heartSpawn = clone $guild->getHeartSpawn();
            $spawn = $heartSpawn->withComponents($heartSpawn->x, $player->getPosition()->getFloorY(), $heartSpawn->z);

            if(($bossbar = BossbarManager::getBossbar($player)) === null) {

                $bossbar = new GuildTerrain($guild->getTag());
                $bossbar->setHealthPercent(($player->getPosition()->distance($spawn) / $guild->getSize()));
                $bossbar->setHealthPercent(1);
                $bossbar->setTitle("§8[" . $color . $guild->getTag() . "§8]§7 - " . $color . $guild->getName());
                $bossbar->showTo($player);

                if(!$guild->existsPlayer($player->getName()) && !$guild->isAlliancePlayer($player->getName())) {

                    if(!($user = Main::getInstance()->getUserManager()->getUser($player->getName())))
                        return;

                    if(!$user->isVanished() && !$player->isCreative()) {
                        foreach($guild->getOnlinePlayers() as $onlinePlayerName => $rank) {
                            $serverPlayer = Server::getInstance()->getPlayerExact($onlinePlayerName);

                            if(!$serverPlayer)
                                continue;

                            $serverPlayer->sendPopup("§cIntruz wkroczyl na teren twojej gildii!");
                        }
                    }
                }

            } else {
                if($bossbar instanceof GuildTerrain) {
                    if($bossbar->getGuildTag() === $guild->getTag())
                        return;

                    $bossbar->setHealthPercent(($player->getPosition()->distance($spawn) / $guild->getSize()));
                    $bossbar->setTitle("§8[" . $color . $guild->getTag() . "§8]§7 - " . $color . $guild->getName());
                }
            }
            $bossbar->updateFor($player);

        } else {
            if(($bossbar = BossbarManager::getBossbar($player)) !== null) {
                if($bossbar instanceof GuildTerrain)
                    $bossbar->hideFrom($player);
            }

            if($player->isAdventure() && !$player->isSpectator())
                $player->setGamemode(GameMode::SURVIVAL());
        }
    }
}