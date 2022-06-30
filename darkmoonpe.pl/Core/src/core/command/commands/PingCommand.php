<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class PingCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("ping", "Ping Command", false, false, "Sluzy do sprawdzania aktualnego pingu gracza!");

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, true),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        $target = $this->selectPlayer($player, $args, 0);

        if(!$target) {
            $player->sendMessage(MessageUtil::format("Ten gracz jest §l§9OFFLINE"));
            return;
        }

        $target->getName() === $player->getName() ? $player->sendMessage(MessageUtil::format("Twoj ping wynosi §l§9".$player->getPing()."§r§7ms")) : $player->sendMessage(MessageUtil::format("Ping gracza §l§9".$target->getName()." §r§7wynosi §l§9".$target->getPing()."§r§7ms"));
    }
}