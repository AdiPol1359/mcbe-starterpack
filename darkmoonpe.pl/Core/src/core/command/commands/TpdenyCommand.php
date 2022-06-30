<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\manager\managers\SoundManager;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;

use core\Main;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class TpdenyCommand extends BaseCommand {

    public function __construct() {

        parent::__construct("tpdeny", "Tpdeny Command", false, false, "Komenda tpdeny sluzy do odrzucania prosb o teleportacje");

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
                $player->sendMessage(MessageUtil::format("Nikt nie wyslal do ciebie prosby o teleportacje"));
                return;
            }

            if(count(Main::$tp[$nick]) == 1) {
                $p = $this->getServer()->getPlayer(key(Main::$tp[$nick]));

                if($p === null){
                    $player->sendMessage(MessageUtil::format("Ten gracz jest §l§9OFFLINE"));
                    return;
                }

                unset(Main::$tp[$nick][$p->getName()]);
                $p->sendMessage(MessageUtil::format("Gracz §9§l$nick §r§7odrzucil twoja prosbe o teleportacje"));
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

        if($p == null || !isset(Main::$tp[$nick][$p->getName()])) {
            $player->sendMessage(MessageUtil::format("Ten gracz nie wyslal tobie porsby o teleportacje"));
            return;
        }

        $p->sendMessage(MessageUtil::format("Gracz §9§l$nick §7odrzucil twoja prosbe o teleportacje"));
        SoundManager::addSound($p, $p->asVector3(), "mob.villager.idle");
    }
}