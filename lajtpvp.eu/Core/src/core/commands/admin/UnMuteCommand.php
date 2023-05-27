<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\Main;
use core\managers\AdminManager;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class UnMuteCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("unmute", "UnMute Commnad", true, true, ["um"]);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(empty($args)) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["nick"]]));
            return;
        }

        $nick = implode(" ", $args);

        if(!Main::getInstance()->getMuteManager()->isMuted($nick)) {
            $sender->sendMessage(MessageUtil::format("Ten gracz nie jest zmutowany!"));
            return;
        }

        Main::getInstance()->getMuteManager()->unMuteNick($nick);
        $sender->sendMessage(MessageUtil::format("Poprawnie odmutowales gracza §e".$nick."§r§7!"));
        AdminManager::sendMessage($sender, $sender->getName() . " odmutowal gracza ".$nick);
    }
}