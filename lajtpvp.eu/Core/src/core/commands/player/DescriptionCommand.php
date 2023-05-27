<?php

declare(strict_types=1);

namespace core\commands\player;

use core\commands\BaseCommand;
use core\items\custom\Safe;
use core\Main;
use core\utils\MessageUtil;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class DescriptionCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("description", "", false, false, ["opis"]);

        $parameters = [
            0 => [
                $this->commandParameter("nazwa", AvailableCommandsPacket::ARG_TYPE_STRING, false),
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(empty($args)) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["opis"]]));
            return;
        }

        $description = implode(" ", $args);

        $item = $sender->getInventory()->getItemInHand();

        if(!Main::getInstance()->getSafeManager()->isSafe($item)) {
            $sender->sendMessage(MessageUtil::format("Trzymany item musi byc sejfem!"));
            return;
        }

        if(strlen($description) > 15) {
            $sender->sendMessage(MessageUtil::format("Opis nie moze przekraczac 15 znakow!"));
            return;
        }

        $safe = Main::getInstance()->getSafeManager()->getSafeById($item->getNamedTag()->getInt("safeId"));

        if($safe->getName() !== $sender->getName()) {
            $sender->sendMessage(MessageUtil::format("Nie mozesz zmienic paternu zablokowanego sejfa!"));
            return;
        }

        $safe->setDescription($description);

        $item = (new Safe($safe))->__toItem();
        $sender->getInventory()->setItemInHand($item);
    }
}