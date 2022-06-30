<?php

namespace core\command\commands;

use core\command\BaseCommand;
use core\fakeinventory\inventory\InvSeeInventory;
use core\Main;
use core\manager\managers\AdminManager;
use core\util\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class InvseeCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("invsee", "Invsee Command", true, false, "Komenda sluzy do wyswietlania ekwipunku gracza");

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $player, array $args) : void {
        if(empty($args)){
            $player->sendMessage($this->correctUse($this->getCommandLabel(), [["nick"]]));
            return;
        }

        $targetPlayer = implode(" ", $args);

        if(!$this->getServer()->hasOfflinePlayerData($targetPlayer) && !$this->getServer()->getPlayer($targetPlayer)){
            $player->sendMessage(MessageUtil::format("Ten gracz nigdy nie gral na serwerze!"));
            return;
        }

        AdminManager::sendMessage(MessageUtil::adminFormat("§l§9" . $player->getName() . " §r§7wlaczyl podglad ekwipunku gracza §l§9".$targetPlayer."§r§7!"), [$player->getName()]);
        ($inv = new InvSeeInventory($player, $targetPlayer))->openFor([$player]);
        Main::$invSeePlayers[$targetPlayer] = $inv;
    }
}