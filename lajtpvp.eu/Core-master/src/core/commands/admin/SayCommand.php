<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\utils\BroadcastUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class SayCommand extends BaseCommand {
    public function __construct() {
        parent::__construct("say", "", true, true);

        $parameters = [
            0 => [
                $this->commandParameter("wiadomosc", AvailableCommandsPacket::ARG_TYPE_STRING, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(count($args) === 0) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["wiadomosc"]]));
            return;
        }

        BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($args, $sender) : void {
            $onlinePlayer->sendMessage("§4§l" .$sender->getName(). " §r§8»§r§c " . implode(" ", $args));
        });
    }
}