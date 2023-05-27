<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\Main;
use core\managers\TeleportManager;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class HomeCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("home", "", false, false);

        $parameters = [
            0 => [
                $this->commandParameter("nazwa", AvailableCommandsPacket::ARG_TYPE_STRING, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        $user = Main::getInstance()->getUserManager()->getUser($sender->getName());

        if(!$user)
            return;

        $homes = $user->getHomeManager()->getHomeNames();

        if(empty($args)) {
            if(empty($homes)) {
                $sender->sendMessage(MessageUtil::format("Nie posiadasz zadnych home'ow!"));
                return;
            }

            $sender->sendMessage(MessageUtil::format("Twoje home'y §e".implode("§7, §e", $homes)));
            return;
        }

        $homeName = implode(" ", $args);

        if(!$user->getHomeManager()->getHome($homeName)) {
            $sender->sendMessage(MessageUtil::format("Home o takiej nazwie nie istnieje!"));
            return;
        }

        if(TeleportManager::isTeleporting($sender->getName())) {
            $sender->sendMessage(MessageUtil::format("Jestes w trakcje teleportacji!"));
            return;
        }

        if(($guilds = Main::getInstance()->getGuildManager()->getGuildFromPos($user->getHomeManager()->getHome($homeName)->getPosition())) !== null) {
            if(!$guilds->existsPlayer($sender->getName())) {
                $sender->sendMessage(MessageUtil::format("Nie mozesz zostac przeteleportowany na home'a poniewaz w jego miejscu znajduje sie obca gildia!"));
                return;
            }
        }

        TeleportManager::teleport($sender, $user->getHomeManager()->getHome($homeName)->getPosition());
    }
}