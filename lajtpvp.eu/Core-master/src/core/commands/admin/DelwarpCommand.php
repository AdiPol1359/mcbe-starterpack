<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\Main;
use core\managers\AdminManager;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class DelwarpCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("delwarp", "", true, false,["deletewarp"]);

        $parameters = [
            0 => [
                $this->commandParameter("nazwa", AvailableCommandsPacket::ARG_TYPE_STRING, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(empty($args)) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["warp"]]));
            return;
        }

        if(!Main::getInstance()->getWarpManager()->getWarp($args[0])) {
            $sender->sendMessage(MessageUtil::format("Ten warp nie istnieje!"));
            return;
        }

        Main::getInstance()->getWarpManager()->deleteWarp($args[0]);
        $sender->sendMessage(MessageUtil::format("Pomyslnie usunieto warpa!"));
        AdminManager::sendMessage($sender, $sender->getName() . " usunal warpa ".$args[0]);
    }
}