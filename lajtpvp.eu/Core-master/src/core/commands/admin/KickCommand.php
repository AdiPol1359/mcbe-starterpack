<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\managers\AdminManager;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class KickCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("kick", "", true, false);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(empty($args)) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["nick"], ["powod"]]));
            return;
        }

        if(($selectedPlayer = $sender->getServer()->getPlayerByPrefix($args[0])) === null) {
            $sender->sendMessage(MessageUtil::format("Ten gracz jest offline!"));
            return;
        }

        array_shift($args);

        $reason = "BRAK";

        if(!empty($args))
            $reason = implode(" ", $args);

        $sender->sendMessage(MessageUtil::format("Wyrzuciles gracza §e".$selectedPlayer->getName(). " §7z serwera"));
        $selectedPlayer->getNetworkSession()->disconnect("§c" . $sender->getName() . " wyrzucil cie z serwera z powodu: " . $reason);
        AdminManager::sendMessage($sender, $sender->getName() . "wyrzucil z serwera gracza " . $selectedPlayer->getName() . " z powodu: " . $reason);
    }
}