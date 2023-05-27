<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\items\custom\PremiumCase;
use core\utils\BroadcastUtil;
use core\utils\InventoryUtil;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class CallCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("call", "", true, true, ["caseall"]);

        $parameters = [
            0 => [
                $this->commandParameter("ilosc", AvailableCommandsPacket::ARG_TYPE_INT, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(empty($args) || !isset($args[0])) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["ilosc"]]));
            return;
        }

        if(!is_numeric($args[0])) {
            $sender->sendMessage(MessageUtil::format("Ilosc musi byc podana w numerach"));
            return;
        }

        $item = (new PremiumCase())->__toItem();
        $item->setCount((int)$args[0]);

        BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($args, $sender, $item) : void {
            InventoryUtil::addItem($item, $onlinePlayer);
            $onlinePlayer->sendMessage(MessageUtil::format("Administrator o nicku §e".$sender->getName()." §r§7rozdal wszystkim §e".$args[0]." §r§7PremiumCase!"));
        });
    }
}