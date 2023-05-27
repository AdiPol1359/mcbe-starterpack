<?php

declare(strict_types=1);

namespace core\tasks\sync;

use core\managers\TeleportManager;
use core\utils\MessageUtil;
use core\utils\PermissionUtil;
use core\utils\Settings;
use core\utils\SoundUtil;
use pocketmine\world\Position;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class TeleportTask extends Task {

    private int $taskTime;
    private int $time;

    public function __construct(private string $nick, int $time, private Position $position) {

        $player = Server::getInstance()->getPlayerExact($nick);

        if(!$player) {
            $this->getHandler()->cancel();
            return;
        }

        $this->taskTime = $time;
        $this->time = ($time + time());

        if(!$position->getWorld())
            $position->world = Server::getInstance()->getWorldManager()->getDefaultWorld();
    }

    public function onRun() : void {

        $player = Server::getInstance()->getPlayerExact($this->nick);

        if(!$player) {
            $this->getHandler()->cancel();
            return;
        }

        if($player->getServer()->isOp($player->getName()) || PermissionUtil::has($player, Settings::$PERMISSION_TAG."fast.teleport")) {
            $player->teleport($this->position->asPosition());
            $player->sendMessage(MessageUtil::format("Poprawnie przeteleportowano!"));
            SoundUtil::addSound([$player], $player->getPosition(), "random.levelup", 100, 10);
            $this->getHandler()->cancel();
            return;
        }

        if($this->time > time()) {
            $time = $this->time - 1;
            if($time < 0)
                $time = 0;

            $taskTime = $this->taskTime - 1;
            if($time < 0)
                $taskTime = 0;

            $player->sendTip("§7Teleportacja... §8" . substr_replace(str_repeat("|", $taskTime), "§e" . str_repeat("|", ($time - time())) . "§8", 0, ($time - time())) . " " . "§r§8(§e".($time - time())."§7s§8)");
        }

        if($this->time <= time()) {
            $player->teleport($this->position);
            $player->sendMessage(MessageUtil::format("Poprawnie przeteleportowano!"));
            SoundUtil::addSound([$player], $player->getPosition(), "random.levelup", 100, 10);
            $this->getHandler()->cancel();
        }
    }

    public function stop() : void {
        $player = Server::getInstance()->getPlayerExact($this->nick);

        $player?->sendMessage(MessageUtil::format("Teleportacja zostala przerwana!"));

        $this->getHandler()->cancel();
    }

    public function onCancel() : void {
        if(TeleportManager::isTeleporting($this->nick))
            TeleportManager::cancelTeleport($this->nick);

        parent::onCancel();
    }
}