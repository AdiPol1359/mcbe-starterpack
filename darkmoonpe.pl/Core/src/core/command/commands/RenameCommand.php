<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class RenameCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("rename", "Rename Command", true, false, "Komenda rename sluzy do zmieniania nazwy itemu", ['itemname']);

        $parameters = [
            0 => [
                $this->commandParameter("nazwa", AvailableCommandsPacket::ARG_TYPE_STRING, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if(empty($args[0])) {
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["nazwa"]]));
            return;
        }

        if(isset($args[0])) {
            $itemName = implode(" ", $args);
            $item = $player->getInventory()->getItemInHand();

            if($item->getId() == 0) {
                $player->sendMessage(MessageUtil::format("Nie mozesz zmienic nazwy lapce!"));
                return;
            }

            $item->setCustomName($itemName);
            $player->getInventory()->setItemInHand($item);
            $player->sendMessage(MessageUtil::format("Poprawnie zmieniles nazwe przedmiotu na §l§9".$itemName."§r§7!"));

        }
    }
}