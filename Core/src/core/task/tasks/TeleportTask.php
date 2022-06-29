<?php

namespace core\task\tasks;

use core\Main;
use core\manager\managers\SoundManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\level\Position;
use pocketmine\scheduler\Task;
use pocketmine\Server;

class TeleportTask extends Task {

    private string $nick;
    private int $time;
    private int $taskTime;
    private Position $position;

    public function __construct(string $nick, int $time, Position $position) {

        $player = Server::getInstance()->getPlayerExact($nick);

        if(!$player) {
            $this->getHandler()->cancel();
            return;
        }

        if(!$player->isOp() && !$player->hasPermission(ConfigUtil::PERMISSION_TAG."fast.teleport"))
            $player->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), (20*$time + 2), 1, false));

        $this->nick = $nick;
        $this->taskTime = $time;
        $this->time = ($time + time());

        if(!$position->level)
            $position->level = Server::getInstance()->getDefaultLevel();

        $this->position = $position;
    }

    public function onRun(int $currentTick) {

        $player = Server::getInstance()->getPlayerExact($this->nick);

        if(!$player) {
            $this->getHandler()->cancel();
            return;
        }

        if($player->isOp() || $player->hasPermission(ConfigUtil::PERMISSION_TAG."fast.teleport")) {
            $player->teleport($this->position->asPosition());
            $player->sendMessage(MessageUtil::format("Poprawnie przeteleportowano!"));
            SoundManager::addSound($player, $player->asPosition(), "random.levelup", 100, 10);
            $this->getHandler()->cancel();
            return;
        }

        if($this->time > time()) {
            $player->sendTip("§7Teleportacja... §l§8" . substr_replace(str_repeat("|", $this->taskTime), "§9" . str_repeat("|", ($this->time - time())) . "§8", 0, ($this->time - time())) . " " . "§r§8(§9".($this->time - time())."§7s§8)");
            SoundManager::addSound($player, $player->asPosition(), "random.click");
        }

        if($this->time <= time()) {
            $player->teleport($this->position);
            $player->sendMessage(MessageUtil::format("Poprawnie przeteleportowano!"));
            SoundManager::addSound($player, $player->asPosition(), "random.levelup", 100, 10);
            $this->getHandler()->cancel();
        }
    }

    public function stop() : void {

        $player = Server::getInstance()->getPlayerExact($this->nick);

        if($player) {
            $player->removeEffect(Effect::BLINDNESS);
            $player->sendMessage(MessageUtil::format("Teleportacja zostala przerwana!"));
        }

        $this->getHandler()->cancel();

    }
    public function onCancel() {

        if(isset(Main::$teleportPlayers[$this->nick]))
            unset(Main::$teleportPlayers[$this->nick]);

        parent::onCancel();
    }
}