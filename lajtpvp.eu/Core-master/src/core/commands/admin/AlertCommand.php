<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\utils\BroadcastUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class AlertCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("alert", "", true, true);

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

        BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($message) : void {
            $onlinePlayer->sendMessage("§8[§l§4ALERT§r§8]§8: §l§c" . $message);
            $onlinePlayer->sendTitle("§l§4ALERT", "§c".$message, 10, 40, 10);
        });
    }
}