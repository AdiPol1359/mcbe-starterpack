<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class DeviceCommand extends BaseCommand{
    public function __construct() {
        parent::__construct("device", "Device Command", true, false, "Komenda device sluzy do sprawdzania z jakiego systemu korzysta gracz", ["platform", "urzedzenie"]);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if(empty($args)){
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["nick"]]));
            return;
        }

        $target = $this->selectPlayer($player, $args, 0);

        if(!$target) {
            $player->sendMessage(MessageUtil::format("Ten gracz jest §l§9OFFLINE"));
            return;
        }

        $device = "NONE";

        switch($target->getDeviceOS()){
            case -1:
                $device = "UNKNOWN";
                break;
            case 1:
                $device = "ANDROID";
                break;
            case 2:
                $device = "IOS";
                break;
            case 3:
                $device = "OSX";
                break;
            case 4:
                $device = "AMAZON";
                break;
            case 7:
                $device = "WINDOWS 10";
                break;
            case 8:
                $device = "WINDOWS 32";
                break;
            case 9:
                $device = "DEDICATED";
                break;
            case 10:
                $device = "TVOS";
                break;
            case 11:
                $device = "PLAYSTATION";
                break;
            case 12:
                $device = "NINTENDO";
                break;
            case 13:
                $device = "XBOX";
                break;
            case 14:
                $device = "WINDOWS PHONE";
                break;
        }

        $player->sendMessage(MessageUtil::format("Gracz o nicku §l§9".$target->getName()."§r§7 korzysta z systemu §l§9".$device));
    }
}