<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\item\items\custom\MagicCase;
use core\util\utils\ConfigUtil;
use core\util\utils\InventoryUtil;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class CallCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("call", "Call Command", true, true, "Komenda pcase sluzy do rozdawania case'ow", ["caseall"]);

        $parameters = [
            0 => [
                $this->commandParameter("ilosc", AvailableCommandsPacket::ARG_TYPE_INT, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if(empty($args) || !isset($args[0])) {
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["ilosc"]]));
            return;
        }

        if(!is_numeric($args[0])) {
            $player->sendMessage(MessageUtil::format("Ilosc musi byc podana w numerach"));
            return;
        }

        $item = new MagicCase();
        $item->setCount($args[0]);

        foreach($this->getServer()->getOnlinePlayers() as $p) {

            if($p->getLevel()->getName() === ConfigUtil::LOBBY_WORLD)
                continue;

            InventoryUtil::addItem($item, $p);
            $p->sendMessage(MessageUtil::format("Administrator o nicku §9§l{$player->getName()} §r§7rozdal wszystkim §9§l{$args[0]} §r§7MagicCase!"));
        }
    }
}