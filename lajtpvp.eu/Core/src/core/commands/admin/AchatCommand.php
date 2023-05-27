<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\utils\BroadcastUtil;
use core\utils\PermissionUtil;
use core\utils\Settings;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class AchatCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("achat", "", true, true, ["a"]);

        $parameters = [
            0 => [
                $this->commandParameter("wiadomosc", AvailableCommandsPacket::ARG_TYPE_STRING, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(empty($args)){
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["wiadomosc"]]));
            return;
        }

        $message = implode(" ", $args);

        BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($sender, $message) : void {
            if(PermissionUtil::has($onlinePlayer, Settings::$PERMISSION_TAG."achat"))
                $onlinePlayer->sendMessage("§l§4[§c@§4] §r§7".$sender->getName()." §8» §c" . $message);
        });
    }
}