<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\managers\AdminManager;
use core\managers\nameTag\NameTagPlayerManager;
use core\users\CorePlayer;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class OpCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("op", "", true, true, ["operator"]);

        $parameters = [
            0 => [
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

        $selectedPlayer = $sender->getServer()->getOfflinePlayer($nick);

        if($sender->getServer()->isOp($selectedPlayer->getName())) {
            $sender->sendMessage(MessageUtil::format("Ten gracz posiada juz uprawnienia operatora!"));
            return;
        }

        $sender->getServer()->addOp($selectedPlayer->getName());
        $sender->sendMessage(MessageUtil::format("Nadales uprawnienia operatora uzytkownikowi §e".$nick));
        AdminManager::sendMessage($sender, $sender->getName()." nadal uprawnienia operatora uzytkownikowi ".$nick);

        if($sender->getServer()->getPlayerByPrefix($selectedPlayer->getName())) {
            NameTagPlayerManager::updatePlayersAround($selectedPlayer);
            $selectedPlayer->sendMessage(MessageUtil::format("Otrzymales uprawnienia operatora od §e" . $sender->getName()));
        }

        if($selectedPlayer instanceof CorePlayer) {
            $selectedPlayer->syncAvailableCommands();
        }
    }
}