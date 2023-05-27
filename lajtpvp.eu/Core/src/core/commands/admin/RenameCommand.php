<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class RenameCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("rename", "", true, false, ['itemname']);

        $parameters = [
            0 => [
                $this->commandParameter("nazwa", AvailableCommandsPacket::ARG_TYPE_STRING, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        if(empty($args[0])) {
            $sender->sendMessage($this->correctUse($this->getCommandLabel(), [["nazwa"]]));
            return;
        }

        if(isset($args[0])) {
            $itemName = implode(" ", $args);
            $item = $sender->getInventory()->getItemInHand();

            if($item->getId() == 0) {
                $sender->sendMessage(MessageUtil::format("Nie mozesz zmienic nazwy lapce!"));
                return;
            }

            $item->setCustomName($itemName);
            $sender->getInventory()->setItemInHand($item);
            $sender->sendMessage(MessageUtil::format("Poprawnie zmieniles nazwe przedmiotu na §e".$itemName."§r§7!"));
        }
    }
}