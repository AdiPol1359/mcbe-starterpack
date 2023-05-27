<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\Main;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class IgnoreCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("ignore", "", false, false);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(empty($args)) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["gracz"]]));
            return;
        }

        $nick = implode(" ", $args);

        if(!Main::getInstance()->getUserManager()->getUser($nick)) {
            $sender->sendMessage(MessageUtil::format("Ten gracz nigdy nie gral na tym serwerze!"));
            return;
        }

        if($nick === $sender->getName()) {
            $sender->sendMessage(MessageUtil::format("Nie mozesz wyciszyc samego siebie!"));
            return;
        }

        $user = Main::getInstance()->getUserManager()->getUser($sender->getName());

        if(!$user)
            return;

        if($user->getIgnoreManager()->isIgnoring($nick)) {
            $sender->sendMessage(MessageUtil::format("Juz masz wyciszonego tego gracza!"));
            return;
        }

        $user->getIgnoreManager()->ignore($nick);
        $sender->sendMessage(MessageUtil::format("Poprawnie wyciszyles gracza §e".$nick));
    }
}