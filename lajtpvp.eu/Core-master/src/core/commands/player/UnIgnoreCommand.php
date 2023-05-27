<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\Main;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class UnIgnoreCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("unignore", "", false, false);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        $user = Main::getInstance()->getUserManager()->getUser($sender->getName());

        if(!$user)
            return;

        $ignoreManager = $user->getIgnoreManager();

        if(empty($args)) {
            if(count($ignoreManager->getIgnoredPlayers()) <= 0)
                $sender->sendMessage(MessageUtil::format("Nie masz zadnego gracza wyciszonego!"));
            else
                $sender->sendMessage(MessageUtil::format("Lista wyciszonych osob: §e" . implode("§7, §e", $ignoreManager->getIgnoredPlayers())));

            return;
        }

        $nick = implode(" ", $args);

        if(!Main::getInstance()->getUserManager()->getUser($nick)) {
            $sender->sendMessage(MessageUtil::format("Ten gracz nigdy nie gral na tym serwerze!"));
            return;
        }

        if(!$ignoreManager->isIgnoring($nick)) {
            $sender->sendMessage(MessageUtil::format("Nie masz wyciszonego tego gracza!"));
            return;
        }

        $ignoreManager->unIgnore($nick);
        $sender->sendMessage(MessageUtil::format("Poprawnie odciszyles gracza §e".$nick));
    }
}