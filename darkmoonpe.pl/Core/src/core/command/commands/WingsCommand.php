<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\manager\managers\AdminManager;
use core\manager\managers\wing\WingsManager;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class WingsCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("wing", "Wings Command", true, false, "Komenda wing sluzy do zarzadzania skrzydalami", ["wings"]);

        $parameters = [
            0 => [
                $this->commandParameter("wingsPlayer", AvailableCommandsPacket::ARG_TYPE_STRING, false, "wingsPlayer", ["daj", "odbierz"]),
                $this->commandParameter("wingsList", AvailableCommandsPacket::ARG_TYPE_TARGET, false, "wingsList", WingsManager::getWingsNames()),
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false)
            ],

            1 => [
                $this->commandParameter("wingsOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "wingsOptions", ["list"]),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        if(empty($args)) {
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["daj", "odbierz", "list"], ["(skrzydla)", "nick"]]));
            return;
        }

        switch(strtolower($args[0])) {
            case "daj":
            case "dodaj":
            case "add":

                if(!isset($args[2])) {
                    $player->sendMessage($this->correctUse($this->getCommandLabel(), [[$args[0]], ["skrzydla"], ["nick"]]));
                    return;
                }

                $wings = WingsManager::getWings($args[1]);

                if($wings === null) {
                    $player->sendMessage(MessageUtil::format("Te skrzydla nie istnieja!"));
                    return;
                }

                $targetPlayer = implode(" ", array_slice($args, 2));

                WingsManager::setPlayerWings($targetPlayer, $wings);

                if($targetPlayer !== $player->getName()) {
                    $player->sendMessage(MessageUtil::format("Nadano graczowi §l§9" . $targetPlayer . " §r§7skrzydla §l§9" . $wings->getName()));
                    AdminManager::sendMessage(MessageUtil::adminFormat("§l§9" . $player->getName() . " §r§7nadal skrzydla §l§9" . $wings->getName() . "§r§7 graczowi §l§9" . $targetPlayer . "§r§7!"), [$player->getName()]);
                } else {
                    $player->sendMessage(MessageUtil::format("Nadales sobie skrzydla §l§9" . $wings->getName()));
                    AdminManager::sendMessage(MessageUtil::adminFormat("§l§9" . $player->getName() . " §r§7nadal sobie skrzydla §l§9" . $wings->getName() . "§r§7!"), [$player->getName()]);
                }

                $p = $player->getServer()->getPlayerExact($targetPlayer);

                if($p !== null)
                    WingsManager::setWings($p, $wings);

                break;
            case "odbierz":
            case "usun":
            case "remove":
                if(!isset($args[1])) {
                    $player->sendMessage($this->correctUse($this->getCommandLabel(), [[$args[0]], ["nick"]]));
                    return;
                }

                if(!WingsManager::hasPlayerWings($args[1])) {
                    $player->sendMessage(MessageUtil::format("Ten gracz nie posiada zadnych skrzydelek!"));
                    return;
                }

                WingsManager::removePlayerWings($args[1]);

                $targetPlayer = implode(" ", array_slice($args, 2));

                if($targetPlayer !== $player->getName()) {
                    $player->sendMessage(MessageUtil::format("Zresetowal graczowi §l§9" . $targetPlayer . " §r§7skrzydla"));
                    AdminManager::sendMessage(MessageUtil::adminFormat("§l§9" . $player->getName() . " §r§7zresetowal skrzydla graczowi §l§9" . $targetPlayer . "§r§7!"), [$player->getName()]);
                } else {
                    $player->sendMessage(MessageUtil::format("Zresetowal sobie skrzydla"));
                    AdminManager::sendMessage(MessageUtil::adminFormat("§l§9" . $player->getName() . " §r§7zresetowal sobie skrzydla"), [$player->getName()]);
                }

                $p = $player->getServer()->getPlayerExact($args[1]);

                if($player !== null)
                    WingsManager::removeWings($p);

                break;
            case "lista":
            case "list":
                $player->sendMessage(MessageUtil::format("Lista dostepnych skrzydel: §l§9" . implode("§r§7, §l§9", WingsManager::getWingsNames())));
                break;
            default:
                $wings = WingsManager::getPlayerWings($args[0]);

                if($wings === null)
                    $player->sendMessage(MessageUtil::format("Ten gracz nie posiada zadnych skrzydelek!"));
                else
                    $player->sendMessage(MessageUtil::format("Skrzydelka gracza §l§9" . $args[0] . "§r§7: §l§9" . $wings->getName()));
        }
    }
}