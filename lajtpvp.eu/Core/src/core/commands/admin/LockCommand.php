<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\Main;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class LockCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("lock", "", true, true);

        $parameters = [
            0 => [
                $this->commandParameter("lockAddOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "lockAddOption", ["add"]),
                $this->commandParameter("lockOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false)
            ],

            1 => [
                $this->commandParameter("lockRemoveOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "lockRemoveOption", ["remove"]),
                $this->commandParameter("lockOptions", AvailableCommandsPacket::ARG_TYPE_STRING, false),
            ],

            2 => [
                $this->commandParameter("lockListOption", AvailableCommandsPacket::ARG_TYPE_STRING, false, "lockListOption", ["list"]),
            ],
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        $commandLockManager = Main::getInstance()->getCommandLockManager();
        
        if(empty($args)) {
            $sender->sendMessage($this->correctUse($this->getCommandLabel(), ["Dodaje komende do listy zablokowanych" => ["add", "§8(§ekomenda§8)"], "Usuwa komende z listy zablokowanych" => ["remove", "§8(§ekomenda§8)"], "Pokazuje liste zablokowanych komend" => ["list"]]));
            return;
        }

        switch($args[0]) {
            case "list":
                $sender->sendMessage(MessageUtil::format("Zablokowane komendy: §e".implode("§7, §e", $commandLockManager->getLockedCommands())));
                break;

            case "add":
                if(!isset($args[1])) {
                    $sender->sendMessage(MessageUtil::format("Nie podales komendy!"));
                    return;
                }

                $command = str_replace("/", "", $args[1]);

                if($commandLockManager->lockCommand($command)) {
                    $sender->sendMessage(MessageUtil::format("Poprawnie zablokowales komende §8/§e".$command));
                } else {
                    $sender->sendMessage(MessageUtil::format("Ta komenda jest juz zablokowana!"));
                }
                break;

            case "remove":
                if(!isset($args[1])) {
                    $sender->sendMessage(MessageUtil::format("Nie podales komendy!"));
                    return;
                }

                if($commandLockManager->unLockCommand($args[1])) {
                    $sender->sendMessage(MessageUtil::format("Poprawnie odblokowales komende §8/§e".$args[1]));
                } else {
                    $sender->sendMessage(MessageUtil::format("Ta komenda nie jest zablokowana!"));
                }

                break;
            default:
                $sender->sendMessage(MessageUtil::format("Nieznany argument!"));
                break;
        }
    }
}