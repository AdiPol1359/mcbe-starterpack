<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\Main;
use core\managers\AdminManager;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class SetwarpCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("setwarp", "", true, false);

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

        if(empty($args)) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["warp"]]));
            return;
        }

        if(Main::getInstance()->getWarpManager()->getWarp($args[0])) {
            $sender->sendMessage(MessageUtil::format("Ten warp juz istnieje!"));
            return;
        }

        Main::getInstance()->getWarpManager()->setWarp($args[0], $sender->getPosition());
        $sender->sendMessage(MessageUtil::format("Pomyslnie ustawiono warpa!"));
        AdminManager::sendMessage($sender, $sender->getName() . " stworzyl warpa ".$args[0]);
    }
}