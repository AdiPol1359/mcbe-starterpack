<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\Main;
use core\manager\managers\SoundManager;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class RCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("r", "R Command", false, false, "Komenda R sluzy do szybkiej odpowiedzi na wiadomosc");

        $parameters = [
            0 => [
                $this->commandParameter("wiadomosc", AvailableCommandsPacket::ARG_TYPE_STRING, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if(empty($args)) {
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["wiadomosc"]]));
            return;
        }

        $message = implode(" ", $args);

        if(!isset(Main::$msg[$player->getName()]) || !($p = $this->getServer()->getPlayerExact(Main::$msg[$player->getName()]))) {
            $player->sendMessage("\n§l§8(§7Ja§8) §9§l{$player->getName()} §r§l§8» §7{$message}\n");
            $player->sendMessage("\n§9§l{$player->getName()} §r§l§8» §7{$message}\n");
            return;
        }

        if(isset(Main::$ignore[$p->getName()])) {
            if(($key = array_search($player->getName(), Main::$ignore[$p->getName()])) === false){
                $player->sendMessage(MessageUtil::format("Ten gracz zablokowal wiadomosci od ciebie!"));
                return;
            }
        }

        $player->sendMessage("§l§8(§7Ja§8) §9§l{$p->getName()} §r§l§8» §7{$message}");
        $p->sendMessage("§9§l{$player->getName()} §r§l§8» §7{$message}");
        SoundManager::addSound($p, $p->asVector3(), "mob.villager.idle");
    }
}