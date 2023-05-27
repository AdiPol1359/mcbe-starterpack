<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\items\custom\PremiumCase;
use core\managers\AdminManager;
use core\utils\InventoryUtil;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class CaseCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("case", "", true, true, ["pcase", "pc"]);

        $parameters = [
            0 => [
                $this->commandParameter("gracz", AvailableCommandsPacket::ARG_TYPE_TARGET, false),
                $this->commandParameter("ilosc", AvailableCommandsPacket::ARG_TYPE_INT, false)
            ],
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        isset($args[0]) ? $targetName = $args[0] : $targetName = $sender->getName();
        !$sender->getServer()->getPlayerByPrefix($targetName) ? $target = null : $target = $sender->getServer()->getPlayerByPrefix($targetName);

        if($target === null) {
            $sender->sendMessage(MessageUtil::format("Ten gracz jest offline"));
            return;
        }

        if(!isset($args[0]) || !isset($args[1])) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["nick"], ["ilosc"]]));
            return;
        }

        if(!is_numeric($args[1])) {
            $sender->sendMessage(MessageUtil::format("Musisz podac ilosc w §eliczbach!"));
            return;
        }

        $item = (new PremiumCase())->__toItem();
        $item->setCount((int)$args[1]);

        InventoryUtil::addItem($item, $target);

        $target->sendMessage(MessageUtil::format("Administrator o nicku §e{$sender->getName()} §r§7dodal ci §e{$args[1]} §r§7PremiumCase"));
        $sender->sendMessage(MessageUtil::format("Poprawnie dodano §e{$args[1]} §r§7PremiumCase dla gracza §e{$target->getName()}"));

        AdminManager::sendMessage($sender, $sender->getName() . " dodal ".$args[1] . " PremiumCase graczowi ". $target->getName());
    }
}