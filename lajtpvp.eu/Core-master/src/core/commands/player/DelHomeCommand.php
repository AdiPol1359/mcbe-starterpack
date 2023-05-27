<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\Main;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class DelHomeCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("delhome", "", false, false);

        $parameters = [
            0 => [
                $this->commandParameter("nazwa", AvailableCommandsPacket::ARG_TYPE_STRING, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(empty($args)) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["nazwa"]]));
            return;
        }

        $user = Main::getInstance()->getUserManager()->getUser($sender->getName());

        if(!$user)
            return;

        $homeName = implode(" ", $args);

        if(!$user->getHomeManager()->getHome($homeName)) {
            $sender->sendMessage(MessageUtil::format("Home o podanej nazwie nie istnieje!"));
            return;
        }

        $sender->sendMessage(MessageUtil::format("Usunales home'a o nazwie §e".$homeName));
        $user->getHomeManager()->deleteHome($homeName);
    }
}