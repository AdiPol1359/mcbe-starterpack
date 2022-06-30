<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\util\utils\ConfigUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class SayCommand extends BaseCommand {
    public function __construct() {
        parent::__construct("say", "Say Command", true, true, "Komenda say sluzy do wysylania wiadomosci tekstowej ( glownie przez konsole )");

        $parameters = [
            0 => [
                $this->commandParameter("wiadomosc", AvailableCommandsPacket::ARG_TYPE_STRING, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if(count($args) === 0) {
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["wiadomosc"]]));
            return;
        }

        foreach($this->getServer()->getOnlinePlayers() as $onlinePlayer) {
            if($onlinePlayer->getLevel()->getName() !== ConfigUtil::LOBBY_WORLD)
                $onlinePlayer->sendMessage("§l§9" .$player->getName(). " §8§l»§r§7 " . implode(" ", $args));
        }
    }
}