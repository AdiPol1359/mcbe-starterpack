<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\Main;
use core\manager\managers\SoundManager;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class MsgCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("msg", "Msg Command", false, false, "Komenda msg sluzy do wysylania prywatnej wiadomosci", ["tell"]);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
                $this->commandParameter("wiadomosc", AvailableCommandsPacket::ARG_TYPE_STRING, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if(!isset($args[0])) {
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["nick"], ["wiadomosc"]]));
            return;
        }

        $p = $this->getServer()->getPlayer(array_shift($args));
        $message = implode(" ", $args);

        if(!$p) {
            $player->sendMessage(MessageUtil::format("Ten gracz jest §l§9OFFLINE"));
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

        Main::$msg[$player->getName()] = $p->getName();
        Main::$msg[$p->getName()] = $player->getName();

        SoundManager::addSound($p, $p->asVector3(), "mob.villager.idle");
    }
}