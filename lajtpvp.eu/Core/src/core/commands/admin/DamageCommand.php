<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\item\Durable;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\player\Player;

class DamageCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("damage", "", true, false, ["meta"]);

        $parameters = [1 => [$this->commandParameter("gamemodeOptions", AvailableCommandsPacket::ARG_TYPE_INT, false),
        ],
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {
        if(!$sender instanceof Player) {
            return;
        }

        $item = $sender->getInventory()->getItemInHand();

        if(empty($args)) {
            $sender->sendMessage(MessageUtil::format("Aktualne id meta trzymanego przedmiotu wynosi: §e" . $item->getMeta()));
            return;
        }

        if(!$item instanceof Durable) {
            $sender->sendMessage(MessageUtil::format("Nie mozesz zmienic ustawienia mety dla tego przemdiotu!"));
            return;
        }

        if(!is_numeric((int)$args[0])) {
            $sender->sendMessage(MessageUtil::format("Wartosc musi byc numeryczna!"));
            return;
        }

        $num = round((int)$args[0]) > $item->getMaxDurability() ? $item->getMaxDurability() : round((int)$args[0]);

        $sender->getInventory()->setItemInHand($item->setDamage((int)$num));
        $sender->sendMessage(MessageUtil::format("Ustawiles id meta trzymanemu przedmiotowi na: §e" . (int)$num));
    }
}