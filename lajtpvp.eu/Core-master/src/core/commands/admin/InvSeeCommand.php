<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\inventories\fakeinventories\invsee\ChoosePlayerInventory;
use core\managers\AdminManager;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class InvSeeCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("invsee", "", true, false);

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

        if(empty($args)){
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["nick"]]));
            return;
        }

        $targetPlayer = implode(" ", $args);
        $selectedPlayer = $sender->getServer()->getPlayerByPrefix($targetPlayer);

        if(!($offlineData = $sender->getServer()->hasOfflinePlayerData($targetPlayer)) && !$selectedPlayer){
            $sender->sendMessage(MessageUtil::format("Ten gracz nigdy nie gral na serwerze!"));
            return;
        }

        if($selectedPlayer)
            $targetPlayer = $selectedPlayer->getName();

        AdminManager::sendMessage($sender, $sender->getName() . " otworzyl ekwipunek gracza " . $targetPlayer);
        (new ChoosePlayerInventory($targetPlayer))->openFor([$sender]);
    }
}