<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\form\forms\report\ReportForm;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class ReportCommand extends BaseCommand {

    public function __construct() {

        parent::__construct("report", "Report Command", false, false, "Komenda report sluzy do zglaszania graczy za np. cheaty", ['helpop']);

        $parameters = [
            0 => [
                $this->commandParameter("wiadomosc", AvailableCommandsPacket::ARG_TYPE_STRING, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        $player->sendForm(new ReportForm());
    }
}