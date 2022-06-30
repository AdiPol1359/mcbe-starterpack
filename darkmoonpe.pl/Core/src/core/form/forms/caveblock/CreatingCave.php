<?php

namespace core\form\forms\caveblock;

use core\{
    caveblock\CaveManager,
    form\forms\Error,
    manager\managers\SoundManager,
    user\UserManager,
    util\utils\MessageUtil,
    util\utils\TimeUtil};
use pocketmine\Player;

class CreatingCave extends CaveForm {

    public function __construct() {

        parent::__construct();

        $data = [
            "type" => "custom_form",
            "title" => "§l§9TWORZENIE JASKINI",
            "content" => []
        ];

        $data["content"][] = ["type" => "input", "text" => "§7Wpisz tutaj §l§9tag§r§7 swojej jaskini", "placeholder" => "", "default" => null];
        $data["content"][] = ["type" => "toggle", "text" => "§7Ogien bratobojczy", "default" => false];
        $data["content"][] = ["type" => "toggle", "text" => "§7Blokada odwiedzania", "default" => false];

        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        $user = UserManager::getUser($player->getName());

        if(!$user)
            return;

        if(strlen($data[0]) > 5) {
            $player->sendForm(new Error($player, "Tag jaskini jest za dlugi!", $this));
            return;
        }

        if(strlen($data[0]) <= 1) {
            $player->sendForm(new Error($player, "Tag jaskini jest za krotki!", $this));
            return;
        }

        if(!ctype_alnum($data[0])) {
            $player->sendForm(new Error($player, "Tag jaskini moze zawierac tylko litery i cyfry!", $this));
            return;
        }

        if(CaveManager::existsCaveExact($data[0])) {
            $player->sendForm(new Error($player, "Jaskinia o takim tagu juz istnieje!", $this));
            return;
        }

        if(!$player->isOp()) {
            if($user->getLastCreateCave() > time()) {
                $player->sendForm(new Error($player, "Mozna zakladac tylko §l§91§r§7 jaskinie na §l§930§r§7 minut! Musisz odczekac jeszcze: §l§9" . TimeUtil::convertIntToStringTime(($user->getLastCreateCave() - time())), $this));
                return;
            }
        }

        CaveManager::createCave($player, $data[0], (int) $data[1], (int) $data[2]);
        $player->sendMessage(MessageUtil::format("Jaskinia zostala poprawnie utworzona mozesz teraz nia zarzadzac dzieki komendzie §l§8/§9cb"));
        SoundManager::addSound($player, $player->asVector3(), "random.pop2");

        $user->setLastCreateCave();
    }
}