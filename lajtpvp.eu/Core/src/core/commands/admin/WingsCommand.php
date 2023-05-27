<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\Main;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class WingsCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("wing", "", true, false, ["wings"]);

        $parameters = [
            0 => [
                $this->commandParameter("addWingsPlayer", AvailableCommandsPacket::ARG_TYPE_STRING, false, "wingsPlayer", ["daj"]),
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
                $this->commandParameter("wingsList", AvailableCommandsPacket::ARG_TYPE_TARGET, false, "wingsList", Main::getInstance()->getWingsManager()->getWingsNames()),
            ],

            1 => [
                $this->commandParameter("takeWingsPlayer", AvailableCommandsPacket::ARG_TYPE_STRING, false, "wingsPlayer", ["odbierz"]),
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ],

            2 => [
                $this->commandParameter("wingsOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "wingsOptions", ["list"]),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(empty($args)) {
            $sender->sendMessage($this->correctUse($this->getCommandLabel(), ["Ustawia dane skrzydla podanemu graczowi" => ["daj", "§8(§enick§8)", "§8(§eskrzydla§8)"], "Odbiera skrzydla danemu graczowi" => ["odbierz", "§8(§enick§8)"], "Pokazuje liste dostepnych skrzydel" => ["list"]]));
            return;
        }

        switch(strtolower($args[0])) {
            case "daj":
            case "dodaj":
            case "add":
                if(!isset($args[2])) {
                    $sender->sendMessage($this->correctUse($this->getCommandLabel(), ["Ustawia dane skrzydla podanemu graczowi" => ["daj", "§8(§enick§8)", "§8(§eskrzydla§8)"], "Odbiera skrzydla danemu graczowi" => ["odbierz", "§8(§enick§8)"], "Pokazuje liste dostepnych skrzydel" => ["list"]]));
                    return;
                }

                $wings = Main::getInstance()->getWingsManager()->getWings($args[2]);

                if($wings === null) {
                    $sender->sendMessage(MessageUtil::format("Te skrzydla nie istnieja!"));
                    return;
                }

                Main::getInstance()->getWingsManager()->setPlayerWings($args[1], $wings);
                $sender->sendMessage(MessageUtil::format("Nadano graczowi §e" . $args[1] . " §r§7skrzydla: §e" . $wings->getName()));

                $p = $sender->getServer()->getPlayerExact($args[1]);

                if($p !== null)
                    Main::getInstance()->getWingsManager()->setWings($p, $wings);
                break;
            case "odbierz":
            case "usun":
            case "remove":
                if(!isset($args[1])) {
                    $sender->sendMessage($this->correctUse($this->getCommandLabel(), ["Ustawia dane skrzydla podanemu graczowi" => ["daj", "§8(§enick§8)", "§8(§eskrzydla§8)"], "Odbiera skrzydla danemu graczowi" => ["odbierz", "§8(§enick§8)"], "Pokazuje liste dostepnych skrzydel" => ["list"]]));
                    return;
                }

                if(!Main::getInstance()->getWingsManager()->hasPlayerWings($args[1])) {
                    $sender->sendMessage(MessageUtil::format("Ten gracz nie posiada zadnych skrzydelek!"));
                    return;
                }

                Main::getInstance()->getWingsManager()->removePlayerWings($args[1]);
                $sender->sendMessage(MessageUtil::format("Odebrano graczowi §e" . $args[1] . " §r§7skrzydla"));
                $p = $sender->getServer()->getPlayerExact($args[1]);

                if($sender !== null)
                    Main::getInstance()->getWingsManager()->removeWings($p);
                break;
            case "lista":
            case "list":
                $sender->sendMessage(MessageUtil::format("Lista dostepnych skrzydel: §e" . implode("§r§7, §e", Main::getInstance()->getWingsManager()->getWingsNames())));
                break;
            default:
                $wings = Main::getInstance()->getWingsManager()->getPlayerWings($args[0]);

                if($wings === null)
                    $sender->sendMessage(MessageUtil::format("Ten gracz nie posiada zadnych skrzydelek!"));
                else
                    $sender->sendMessage(MessageUtil::format("Skrzydelka gracza §e" . $args[0] . "§r§7: §e" . $wings->getName()));
        }
    }
}