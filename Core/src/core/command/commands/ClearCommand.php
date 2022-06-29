<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\manager\managers\AdminManager;
use core\util\utils\MessageUtil;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\command\CommandSender;

class ClearCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("clear", "Clear Command", true, true, "Komenda clear sluzy do czyszczenia ekwipunku gracza");

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, true),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        $target = $this->selectPlayer($player, $args, 0);

        if(!$target) {
            $player->sendMessage(MessageUtil::format("Ten gracz jest §l§9OFFLINE"));
            return;
        }

        $target->getInventory()->clearAll();
        $target->getArmorInventory()->clearAll();
        if($target !== $player) {
            AdminManager::sendMessage(MessageUtil::adminFormat("§l§9" . $player->getName() . " §r§7wyczyscil ekwipunek gracza §l§9" . $target->getName() . "§r§7!"), [$player->getName()]);
            $target->sendMessage(MessageUtil::format("Administrator o nicku §l§9" . $player->getName() . " §r§7wyczyscil ci ekwipunek!"));
            $player->sendMessage(MessageUtil::format("Pomyslnie wyczyszczono ekwipunek gracza §9§l{$target->getName()}"));
        } else {
            AdminManager::sendMessage(MessageUtil::adminFormat("§l§9" . $player->getName() . " §r§7wyczyscil sobie ekwipunek"), [$player->getName()]);
            $player->sendMessage(MessageUtil::format("Wyczysciles sobie ekwipunek!"));
        }
    }
}