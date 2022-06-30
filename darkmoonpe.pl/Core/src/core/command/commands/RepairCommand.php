<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\fakeinventory\inventory\upgrader\BlackSmithInventory;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class RepairCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("repair", "Repair Command", false, false, "Komenda repair sluzy do naprawy przedmiotow", ["napraw"]);

        $parameters = [
            0 => [
                $this->commandParameter("repairOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "repairOptions", ["all"])
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        if(empty($args)) {
            (new BlackSmithInventory($player))->openFor([$player]);
            return;
        }

        if(isset($args[0])) {
            if($args[0] === "all") {

                if(!$player->hasPermission(ConfigUtil::PERMISSION_TAG."repair.all")) {
                    $player->sendMessage(MessageUtil::formatLines($this->permissionMessage(ConfigUtil::PERMISSION_TAG."repair.all")));
                    return;
                }

                foreach($player->getInventory()->getContents() as $slot => $item)
                    $player->getInventory()->setItem($slot, $item->setDamage(0));

                foreach($player->getArmorInventory()->getContents() as $slot => $item)
                    $player->getArmorInventory()->setItem($slot, $item->setDamage(0));

                $player->sendMessage(MessageUtil::format("Poprawnienie naprawiono wszystkie itemy w twoim ekwipunku!"));
            } else
                $player->sendMessage($this->correctUse($this->getCommandLabel(), [["all"]]));
        }
    }
}