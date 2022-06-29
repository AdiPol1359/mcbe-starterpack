<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\manager\managers\SoundManager;
use core\task\tasks\TeleportTask;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\level\Position;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\Player;
use core\Main;

class TpacceptCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("tpaccept", "Tpaccept Command", false, false, "Komenda tpaccept sluzy do akceptacji prosby o teleportacje");

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        $nick = $player->getName();

        if(empty($args)) {
            if(empty(Main::$tp[$nick])) {
                $player->sendMessage(MessageUtil::format("Nie masz zadnych prosb o teleportacje"));
                return;
            }

            if(count(Main::$tp[$nick]) === 1) {
                $p = $this->getServer()->getPlayer(key(Main::$tp[$nick]));

                if($p === null){
                    $player->sendMessage(MessageUtil::format("Ten gracz jest §l§9OFFLINE"));
                    return;
                }

                $this->teleportProcess($player, $p);
            } else {
                $player->sendMessage(MessageUtil::format("Prosby o teleportacje do ciebie od graczy: "));

                $requests = [];

                foreach(Main::$tp[$nick] as $p => $time)
                    $requests[] = $p;

                $player->sendMessage(MessageUtil::format(implode("§l§7, §9", $requests)));
            }
            return;
        }

        $p = $this->getServer()->getPlayer($args[0]);

        if($p === null || !isset(Main::$tp[$nick][$p->getName()])) {
            $player->sendMessage(MessageUtil::format("Ten gracz nie wyslal do ciebie prosby o teleportacje"));
            return;
        }

        $this->teleportProcess($player, $p);
    }

    private function teleportProcess(Player $player, Player $teleportPlayer) {
        $nick = $player->getName();
        $teleportPlayerNick = $teleportPlayer->getName();

        if(Main::$tp[$nick][$teleportPlayerNick] - time() <= 0) {
            $player->sendMessage(MessageUtil::format("Prosba o teleportacje wygasla"));
            unset(Main::$tp[$nick][$teleportPlayerNick]);
            return;
        }

        unset(Main::$tp[$nick][$teleportPlayerNick]);

        $player->sendMessage(MessageUtil::format("Zaakceptowales prosbe o teleportacje od gracza §9§l{$teleportPlayerNick}"));
        $teleportPlayer->sendMessage(MessageUtil::format("Gracz §9§l{$nick} §r§7zaakceptowal twoja porsbe o teleportacje"));
        SoundManager::addSound($teleportPlayer, $teleportPlayer->asVector3(), "mob.villager.idle");

        if(isset(Main::$teleportPlayers[$teleportPlayer->getName()])) {
            $player->sendMessage(MessageUtil::format("Jestes w trakcje teleportacji!"));
            return;
        }

        if($teleportPlayer->getLevel()->getName() === ConfigUtil::PVP_WORLD) {
            Main::$teleportPlayers[$teleportPlayer->getName()] = Main::getInstance()->getScheduler()->scheduleRepeatingTask(new TeleportTask($teleportPlayer->getName(), ConfigUtil::TELEPORT_TIME, Position::fromObject($player->asPosition())), 20);
            return;
        }

        $teleportPlayer->teleport($player->asPosition());
    }
}