<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\items\custom\Crowbar;
use core\managers\AdminManager;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class CrowbarCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("crowbar", "", true, true, ["lom"]);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        if(empty($args)) {
            $sender->getInventory()->addItem((new Crowbar())->__toItem());
            $sender->sendMessage(MessageUtil::format("Dodales sobie lom!"));
            AdminManager::sendMessage($sender, $sender->getName()." dodal sobie lom");
            return;
        }

        $nick = implode(" ", $args);

        if(($selectedPlayer = $sender->getServer()->getPlayerExact($nick)) === null) {
            $sender->sendMessage(MessageUtil::format("Ten gracz jest offline!"));
            return;
        }

        $selectedPlayer->getInventory()->addItem((new Crowbar())->__toItem());
        $sender->sendMessage(MessageUtil::format("Dodales lom graczowi §e".$selectedPlayer->getName()));
        AdminManager::sendMessage($sender, $sender->getName()." dodal lom graczowi §e".$selectedPlayer->getName());
    }
}