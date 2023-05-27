<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\Main;
use core\managers\AdminManager;
use core\utils\MessageUtil;
use core\utils\TimeUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class TurboDropCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("turbodrop", "", true, true, ["tb"]);

        $parameters = [
            0 => [
                $this->commandParameter("turbodropServerOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "turbodropServerOption", ["server"]),
                $this->commandParameter("turbodropTime", AvailableCommandsPacket::ARG_TYPE_STRING, false),
            ],

            1 => [
                $this->commandParameter("turbodropPlayerOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "turbodropPlayerOption", ["player"]),
                $this->commandParameter("turbodropTime", AvailableCommandsPacket::ARG_TYPE_STRING, false),
                $this->commandParameter("turbodropPlayer", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(empty($args) || !isset($args[1])) {
            $sender->sendMessage($this->correctUse($this->getCommandLabel(), ["Wlacza turbodrop globalnie" => ["server", "§8{§eczas§8(§es§7/§em§7/§eh§7/§ed§8)§8}"], "Wlacza turbodrop dla pojedynczego gracza" => ["player", "§8{§eczas§8(§es§7/§em§7/§eh§7/§ed§8)§8}", "§8(§enick§8)"]]));
            return;
        }

        if(($time = TimeUtil::getTimeFromFormat($args[1], true)) === null) {
            $sender->sendMessage(MessageUtil::format("Nieprawidlowy format czasu!"));
            return;
        }

        switch($args[0]) {
            case "server":
                Main::getInstance()->getTurboDropManager()->addTurboDrop("SERVER", true, $time);
                $sender->sendMessage(MessageUtil::format("Poprawnie nadales turbodrop serwerowy na §e".($resultTime = TimeUtil::convertIntToStringTime($time - time(), "§e", "§7", false, true))));

                AdminManager::sendMessage($sender, $sender->getName() . " wlaczyl TurboDrop na serwerze na ".$resultTime);
                break;

            case "player":
                if(!isset($args[2])) {
                    $sender->sendMessage(MessageUtil::format("Nie podales nazwy gracza"));
                    return;
                }

                $selectedNick = implode(" ", array_slice($args, 2));
                Main::getInstance()->getTurboDropManager()->addTurboDrop($selectedNick, false, $time);
                $sender->sendMessage(MessageUtil::format("Poprawnie nadales turbodrop graczowi §e".$sender->getName()."§7 na §e".($resultTime = TimeUtil::convertIntToStringTime($time - time(), "§e", "§7", false, true))));
                AdminManager::sendMessage($sender, $sender->getName() . " wlaczyl TurboDrop dla gracza ".$selectedNick." na ".$resultTime);
                break;

            default:
                $sender->sendMessage(MessageUtil::format("Nieznany argument!"));
                break;
        }
    }
}