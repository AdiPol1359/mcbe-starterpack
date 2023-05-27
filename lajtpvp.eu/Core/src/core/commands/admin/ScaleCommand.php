<?php

declare(strict_types=1);

namespace core\commands\admin;

use core\commands\BaseCommand;
use core\managers\AdminManager;
use core\utils\MessageUtil;
use Exception;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;

class ScaleCommand extends BaseCommand {

    public function __construct() {
        parent::__construct("scale", "", true, false);

        $parameters = [
            0 => [
                $this->commandParameter("wielkosc", AvailableCommandsPacket::ARG_TYPE_FLOAT, false)
            ]
        ];

        $this->setOverLoads($parameters);
    }

    public function onCommand(CommandSender $sender, array $args) : void {

        if(empty($args) || ($args[0] !== "default" && !is_numeric($args[0]))) {
            $sender->sendMessage($this->simpleCommandCorrectUse($this->getCommandLabel(), [["0.1-10", "default"], ["nick"]]));
            return;
        }

        $target = $this->selectPlayer($sender, $args, 1);

        if($target === null) {
            $sender->sendMessage(MessageUtil::format("Ten gracz jest offline!"));
            return;
        }

        if($args[0] === "default") {
            $target->setScale(1);

            if($target->getName() === $sender->getName()) {
                $target->sendMessage(MessageUtil::format("Ustawiono domyslna wielkosc!"));
                AdminManager::sendMessage($sender, $sender->getName() . " ustawil sobie domyslna skale");
            } else {
                $sender->sendMessage(MessageUtil::format("Ustawiles wielkosc domyslna graczu §e" . $target->getName()));
                $sender->sendMessage(MessageUtil::format("Twoja wielkosc zostala zmnieniona na domyslna przez administratora §e".$sender->getName()));
                AdminManager::sendMessage($sender, $sender->getName() . " ustawil domyslna skale graczowi " . $target->getName());
            }
            return;
        }

        $scale = (float)number_format(floatval($args[0]), 2, ".", "");

        if($scale <= 0 || $scale > 5) {
            $sender->sendMessage(MessageUtil::format("Wartosc musi byc wieksza jak §e0 §7i mniejsza jak §e5"));
            return;
        }

        try {
            $target->setScale($scale);
        }catch (Exception $err) {
            var_dump($err);
            return;
        }

        if($target->getName() === $sender->getName()) {
            $target->sendMessage(MessageUtil::format("Ustawiono skale §e" . $scale));
            AdminManager::sendMessage($sender, $sender->getName() . " ustawil sobie skale " . $scale);
        } else {
            $sender->sendMessage(MessageUtil::format("Ustawiles skale §e".$scale." §7graczu §e" . $target->getName()));
            $sender->sendMessage(MessageUtil::format("Twoja wielkosc zostala zmnieniona na §e".$scale." §7przez administratora §e".$sender->getName()));
            AdminManager::sendMessage($sender, $sender->getName() . " ustawil sobie skale " . $scale . " graczowi ".$target->getName());
        }
    }
}