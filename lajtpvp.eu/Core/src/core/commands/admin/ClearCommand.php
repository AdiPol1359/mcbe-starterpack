<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\managers\AdminManager;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class ClearCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("clear", "", true, false);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        $selectName = !empty($args) ? implode(" ", $args) : $sender->getName();
        $selectedPlayer = $sender->getServer()->getPlayerExact($selectName);

        if(!$selectedPlayer) {
            $sender->sendMessage(MessageUtil::format("Ten gracz jest offline!"));
            return;
        }

        $selectedPlayer->getInventory()->clearAll();
        $selectedPlayer->getArmorInventory()->clearAll();

        if($selectedPlayer->getName() === $sender->getName()) {
            $sender->sendMessage(MessageUtil::format("Wyczysciles sobie ekwipunek!"));
            AdminManager::sendMessage($sender, $sender->getName() . " wyczyscil swoj ekwipunek");
        } else {
            $selectedPlayer->sendMessage(MessageUtil::format("Administrator §e".$sender->getName()."§r§7 wyczyscil tobie ekwipunek!"));
            $sender->sendMessage(MessageUtil::format("Wyczysciles ekwipuenk gracza §e".$selectedPlayer->getName()."§r§7!"));
            AdminManager::sendMessage($sender, $sender->getName() . " wyczyscil ekwipunek gracza " . $selectedPlayer->getName());
        }
    }
}