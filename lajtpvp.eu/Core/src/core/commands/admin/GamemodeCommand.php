<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\managers\AdminManager;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\GameMode;

class GamemodeCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("gamemode", "", true, false, ["gm"], [
            0 => [
                $this->commandParameter("gamemodeOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "gamemodeOptions", ["0", "1", "2", "3"]),
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false)
            ],

            1 => [
                $this->commandParameter("gamemodeOptions", AvailableCommandsPacket::ARG_TYPE_INT,false),
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false)
            ],

            2 => [
                $this->commandParameter("gamemodeOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "gamemodeOptions", ["0", "1", "2", "3"])
            ],

            3 => [
                $this->commandParameter("gamemodeOptions", AvailableCommandsPacket::ARG_TYPE_INT, false),
            ],
        ]);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(empty($args)) {
            $this->simpleCommandCorrectUse($this->getCommandLabel(), [["gamemode"], ["nick"]]);
            return;
        }

        $target = $this->selectPlayer($sender, $args, 1);

        if($target === null) {
            $sender->sendMessage(MessageUtil::format("Ten gracz jest §eOFFLINE"));
            return;
        }

        $gamemode = GameMode::fromString($args[0]);

        if($gamemode === null) {
            $sender->sendMessage(MessageUtil::format("Nieznany tryb gry!"));
            return;
        }

        if($target !== $sender) {
            $target->sendMessage(MessageUtil::format("Administrator o nicku §e{$sender->getName()}§r §7ustawil ci tryb gry §e" . strtoupper($gamemode->getEnglishName())));
            $sender->sendMessage(MessageUtil::format("Poprawnie ustawiles tryb gry §e" . strtoupper($gamemode->getEnglishName()) . " §r§7graczu o nicku §e" . $target->getName()));
            AdminManager::sendMessage($sender, $sender->getName() . " zmienil tryb gry gracza " . $target->getName() . " na " . strtoupper($gamemode->getEnglishName()));
        } else {
            $target->sendMessage(MessageUtil::format("Ustawiles sobie tryb gry na §e" . strtoupper($gamemode->getEnglishName())));
            AdminManager::sendMessage($sender, $sender->getName() . " zmienil swoj tryb gry na " . strtoupper($gamemode->getEnglishName()));
        }

        $target->setGamemode($gamemode);
    }
}