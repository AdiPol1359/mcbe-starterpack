<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\manager\managers\SoundManager;
use pocketmine\command\CommandSender;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class AlertCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("alert", "Alert Command", true, true, "Komenda alert sluzy do wysylania wiadomosci do wszystkich graczy");

        $parameters = [
            0 => [
                $this->commandParameter("wiadomosc", AvailableCommandsPacket::ARG_TYPE_STRING, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if(empty($args)) {
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["wiadomosc"]]));
            return;
        }

        $alert = implode(" ", $args);
        foreach($this->getServer()->getOnlinePlayers() as $p) {
            $p->sendMessage("§4§lALERT §8» §r§c" . $alert);
            $p->addTitle("§4§lALERT", "§r§c" . $alert, 20*1, 20*5, 20*1);
            SoundManager::addSound($p, $player->asVector3(), "random.explode", 1);
        }
    }
}