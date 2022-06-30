<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\item\items\custom\MagicCase;
use core\util\utils\InventoryUtil;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class CaseCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("case", "Case Command", true, true, "Komenda sluzy do rozdania magicaseow dla danego gracza");

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
                $this->commandParameter("ilosc", AvailableCommandsPacket::ARG_TYPE_INT, false)
            ],

            1 => [
                $this->commandParameter("ilosc", AvailableCommandsPacket::ARG_TYPE_INT, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        isset($args[0]) ? $targetName = $args[0] : $targetName = $player->getName();
        !$this->getServer()->getPlayer($targetName) ? $target = null : $target = $this->getServer()->getPlayer($targetName);

        if($target === null) {
            $player->sendMessage(MessageUtil::format("Ten gracz jest §l§9OFFLINE"));
            return;
        }

        if(!isset($args[0]) || !isset($args[1])) {
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["nick"], ["ilosc"]]));
            return;
        }

        if(!is_numeric($args[1])) {
            $player->sendMessage(MessageUtil::format("Musisz podac ilosc w §9liczbach!"));
            return;
        }

        $item = new MagicCase();
        $item->setCount($args[1]);

        InventoryUtil::addItem($item, $target);

        $target->sendMessage(MessageUtil::format("Administrator o nicku §l§9{$player->getName()} §r§7dodal ci §l§9{$args[1]} §r§7MagicCase"));
        $player->sendMessage(MessageUtil::format("Poprawnie dodano §l§9{$args[1]} §r§7MagicCase dla gracza §l§9{$target->getName()}"));
    }
}