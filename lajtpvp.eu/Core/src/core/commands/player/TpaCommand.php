<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\Main;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class TpaCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("tpa", "", true, false);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ],

            1 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(empty($args)) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["nick"]]));
            return;
        }

        $nick = implode(" ", $args);

        if(($selectedPlayer = $sender->getServer()->getPlayerByPrefix($nick)) === null) {
            $sender->sendMessage(MessageUtil::format("Ten gracz jest offline!"));
            return;
        }

        if($selectedPlayer->getName() === $sender->getName()) {
            $sender->sendMessage(MessageUtil::format("Nie mozesz wyslac prosby o teleportacje do samego siebie!"));
            return;
        }

        $selectedUser = Main::getInstance()->getUserManager()->getUser($selectedPlayer->getName());

        if(!$selectedUser)
            return;

        if($selectedUser->hasTeleportRequest($sender->getName())) {
            $sender->sendMessage(MessageUtil::format("Ten gracz ma juz jedna twoja prosbe o teleportacje!"));
            return;
        }

        $selectedUser->setTeleportRequest($sender->getName());

        $selectedPlayer->sendMessage(MessageUtil::formatLines(["Gracz §e".$sender->getName(). " §7wyslal prosbe o teleportacje!", "Aby ja zaakceptowac wpisz §8/§etpaccept"], "TELEPORTACJA"));
        $sender->sendMessage(MessageUtil::format("Wyslales prosbe o teleportacje do gracza §e".$selectedPlayer->getName()));
    }
}