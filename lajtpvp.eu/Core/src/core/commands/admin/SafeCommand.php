<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\SafeInventory;
use core\Main;
use core\managers\AdminManager;
use core\utils\MessageUtil;
use core\utils\SoundUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;
use pocketmine\Server;

class SafeCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("safe", "", true, true, ["sejf"]);

        $parameters = [
            0 => [
                $this->commandParameter("safeOpenOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "safeOpenOptions", ["open"]),
                $this->commandParameter("id", AvailableCommandsPacket::ARG_TYPE_INT, false),
            ],

            1 => [
                $this->commandParameter("safeAddOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "safeAddOptions", ["add"]),
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ],

            2 => [
                $this->commandParameter("safeDeleteOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false, "safeDeleteOptions", ["delete"]),
                $this->commandParameter("id", AvailableCommandsPacket::ARG_TYPE_INT, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        if(empty($args)) {
            $sender->sendMessage($this->correctUse($this->getCommandLabel(), ["Otwiera sejf o podanym id" => ["open", "id"], "Dodaje sejf podanemu graczowi" => ["add", "nick"], "Usuwa sejf o podanym id" => ["delete", "id"]]));
            return;
        }

        switch($args[0]) {

            case "open":

                if(!isset($args[1])) {
                    $sender->sendMessage(MessageUtil::format("Nie podano id sejfu!"));
                    return;
                }

                if(!is_numeric($args[1])) {
                    $sender->sendMessage(MessageUtil::format("Id musi byc numeryczne!"));
                    return;
                }

                $id = (int) $args[1];

                if(($safe = Main::getInstance()->getSafeManager()->getSafeById($id)) === null) {
                    $sender->sendMessage(MessageUtil::format("Sejf o podanym id nie istnieje!"));
                    return;
                }

                AdminManager::sendMessage($sender, $sender->getName() . " otworzyl sejf o id " . $safe->getSafeId());

                SoundUtil::addSound([$sender], $sender->getPosition(), "random.shulkerboxopen");
                (new SafeInventory($safe))->openFor([$sender]);

                break;

            case "add":

                $selectedPlayer = $sender;

                if(isset($args[1])) {
                    $arguments = $args;
                    array_shift($arguments);

                    ($onlinePlayer = Server::getInstance()->getPlayerExact(implode(" ", $arguments))) ? $selectedPlayer = $onlinePlayer : null;
                }

                Main::getInstance()->getSafeManager()->addSafe($selectedPlayer);
                $sender->sendMessage(MessageUtil::format("Dodano sejf!"));
                AdminManager::sendMessage($sender, $sender->getName() . " dodal sejf graczowi " . $selectedPlayer->getName());
                break;

            case "delete":

                if(!isset($args[1])) {
                    $sender->sendMessage(MessageUtil::format("Nie podano id sejfu!"));
                    return;
                }

                if(!is_numeric($args[1])) {
                    $sender->sendMessage(MessageUtil::format("Id sejfa musi byc numeryczna!"));
                    return;
                }

                $id = (int) $args[1];

                if(($safe = Main::getInstance()->getSafeManager()->getSafeById($id)) === null) {
                    $sender->sendMessage(MessageUtil::format("Sejf o podanym id nie istnieje!"));
                    return;
                }

                Main::getInstance()->getSafeManager()->deleteSafe($safe->getSafeId());

                AdminManager::sendMessage($sender, $sender->getName() . " usunal sejf o id ".$safe->getSafeId());

                $sender->sendMessage(MessageUtil::format("Poprawnie usunieto sejf!"));
                break;
        }
    }
}