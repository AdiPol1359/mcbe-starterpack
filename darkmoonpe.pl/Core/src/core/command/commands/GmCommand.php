<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\manager\managers\AdminManager;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class GmCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("gm", "Gm Command", true, false, "Komenda gm ustawia tryb gry", ["gamemode", "gmode"]);

        $parameters = [
            0 => [
                $this->commandParameter("gamemodeOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "gamemodeOptions", [0, 1, 2, 3]),
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, true)
            ],

            1 => [
                $this->commandParameter("gamemodeOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "gamemodeOptions", [0, 1, 2, 3]),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {

        $target = $this->selectPlayer($player, $args, 1);

        if($target === null) {
            $player->sendMessage(MessageUtil::format("Ten gracz jest §l§9OFFLINE"));
            return;
        }

        isset($args[0]) ? $gamemode = $this->getServer()->getGamemodeFromString($args[0]) : $gamemode = -1;

        if($gamemode === -1) {
            $player->sendMessage(MessageUtil::format("Nieznany tryb gry!"));
            return;
        }

        $gmName = $this->getServer()->getGamemodeName($gamemode);

        if($target !== $player) {
            AdminManager::sendMessage(MessageUtil::format("§l§9" . $player->getName() . " §r§7zmienil tryb gry gracza §l§9" . $target->getName() . "§r§7 na §l§9" . $gmName . "§r§7!"), [$player->getName()]);
            $target->sendMessage(MessageUtil::format("Administrator o nicku §9§l{$player->getName()}§r §7ustawil ci tryb gry §9§l" . strtoupper($gmName)));
            $player->sendMessage(MessageUtil::format("Poprawnie ustawiles tryb gry §9§l" . strtoupper($gmName) . " §r§7graczu o nicku §l§9" . $target->getName()));
        } else {
            AdminManager::sendMessage(MessageUtil::adminFormat("§l§9" . $player->getName() . " §r§7zmienil swoj tryb gry na §l§9" . $gmName . "§r§!"), [$player->getName()]);
            $target->sendMessage(MessageUtil::format("Ustawiles sobie tryb gry na §9§l" . strtoupper($gmName)));
        }

        $target->setGamemode($gamemode);
    }
}