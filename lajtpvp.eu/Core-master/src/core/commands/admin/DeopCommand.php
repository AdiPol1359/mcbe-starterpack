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

class DeopCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("deop", "", true, true, ["deoperator"]);

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

        if(!$sender->getServer()->isOp($selectedPlayer->getName())) {
            $sender->sendMessage(MessageUtil::format("Ten gracz nie posiada uprawnien operatora!"));
            return;
        }

        $sender->getServer()->removeOp($selectedPlayer->getName());
        $sender->sendMessage(MessageUtil::format("Zabrales uprawnienia operatora uzytkownikowi §e".$nick));
        AdminManager::sendMessage($sender, $sender->getName()." zabral uprawnienia operatora uzytkownikowi §e".$nick);

        if($sender->getServer()->getPlayerByPrefix($selectedPlayer->getName())) {
            NameTagPlayerManager::updatePlayersAround($selectedPlayer);
            $selectedPlayer->sendMessage(MessageUtil::format("Administrator §e" . $sender->getName() . " §7zabral ci uprawnienia operatora"));
        }

        if($selectedPlayer instanceof CorePlayer) {
            $selectedPlayer->syncAvailableCommands();
        }
    }
}