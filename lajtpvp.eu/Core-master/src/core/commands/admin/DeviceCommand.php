<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class DeviceCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("device", "", true, true, ["os"]);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(empty($args)){
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["nick"]]));
            return;
        }

        $target = $this->selectPlayer($sender, $args, 0);

        if(!$target) {
            $sender->sendMessage(MessageUtil::format("Ten gracz jest §eOFFLINE"));
            return;
        }

        $device = match ($target->getPlayerInfo()->getExtraData()["DeviceOS"]) {
            -1 => "UNKNOWN",
            1 => "ANDROID",
            2 => "IOS",
            3 => "OSX",
            4 => "AMAZON",
            7 => "WINDOWS 10",
            8 => "WINDOWS 32",
            9 => "DEDICATED",
            10 => "TVOS",
            11 => "PLAYSTATION",
            12 => "NINTENDO",
            13 => "XBOX",
            14 => "WINDOWS PHONE",
        };

        $sender->sendMessage(MessageUtil::format("Gracz o nicku §e".$target->getName()."§r§7 korzysta z systemu §e".$device));
    }
}