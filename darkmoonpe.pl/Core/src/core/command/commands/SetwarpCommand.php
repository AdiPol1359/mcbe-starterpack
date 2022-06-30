<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;

use core\manager\managers\WarpManager;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class SetwarpCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("setwarp", "SetWarp Command", true, false, "Komenda setwarp sluzy do ustawiania warpa");

        $parameters = [
            0 => [
                $this->commandParameter("nazwa", AvailableCommandsPacket::ARG_TYPE_STRING, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if(empty($args)) {
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["warp"]]));
            return;
        }

        if(WarpManager::isWarpExists($args[0])) {
            $player->sendMessage(MessageUtil::format("Ten warp juz istnieje!"));
            return;
        }

        WarpManager::setWarp($args[0], $player);
        $player->sendMessage(MessageUtil::format("Pomyslnie ustawiono warpa!"));
    }
}