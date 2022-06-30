<?php

namespace core\form\forms\caveblock;

use core\caveblock\CaveManager;
use core\form\forms\Error;
use core\Main;
use core\util\utils\MessageUtil;
use pocketmine\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;

class SendRequest extends CaveForm {

    public function __construct(Player $player) {

        parent::__construct();

        $data = [
            "type" => "custom_form",
            "title" => "§9§lZAPRASZANIE GRACZA",
            "content" => []
        ];

        $playerCaves = [];

        foreach(CaveManager::getCaves($player->getName()) as $cave){
            if($cave->isOwner($player->getName()) || $cave->getPlayerSetting($player->getName(), "z_perm"))
                $playerCaves[] = $cave->getName();
        }

        $data["content"][] = ["type" => "input", "text" => "§7Wpisz nick gracza do ktorego chcesz wyslac zaproszenie!", "placeholder" => "Steve", "default" => null];
        $data["content"][] = ["type" => "dropdown", "text" => "§7Wybierz jaskienie do ktorej chcesz zaprosic", "options" => $playerCaves, "default" => array_key_first($playerCaves)];
        $data["content"][] = ["type" => "label", "text" => "\n"];

        $dataContent = array_values($playerCaves);

        $data["caves"] = $dataContent;

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if(empty($data[0])) {
            $player->sendForm(new ChooseRequest());
            return;
        }

        $p = Server::getInstance()->getPlayer($data[0]);

        $clickedCave = "";

        foreach($this->data["caves"] as $id => $name) {
            if($id === $data[1])
                $clickedCave = $name;
        }

        $playerCaves = [];

        foreach(CaveManager::getCaves($player->getName()) as $cave){
            if($cave->isOwner($player->getName()) || $cave->getPlayerSetting($player->getName(), "z_perm"))
                $playerCaves[] = $cave->getName();
        }

        if(!in_array($clickedCave, $playerCaves)) {
            $player->sendForm(new Error($player, "Ta jaskinia nie nalezy do ciebie!", $this));
            return;
        }

        if($p === null) {
            $player->sendForm(new Error($player, "Gracz o nicu §9§l" . $data[0] . " §r§7nie jest online!", $this));
            return;
        }

        if($data[0] === $player->getName()) {
            $player->sendForm(new Error($player, "Nie mozesz zaprosic samego siebie!", $this));
            return;
        }

        if(($key = array_search([$player->getName() => $clickedCave], Main::$request[$p->getName()])) !== false) {
            $player->sendForm(new Error($player, "Ten gracz ma juz jedno oczekujace zaproszenie od ciebie!", $this));
            return;
        }

        $cave = CaveManager::getCaveByTag($clickedCave);

        if($cave->isMember($p->getName())) {
            $player->sendForm(new Error($player, "Ten gracz juz nalezy do tej jaskini!", $this));
            return;
        }

        Main::$request[$p->getName()][] = [$player->getName() => $clickedCave];

        $playerName = $p->getName();

        Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($clickedCave, $data, $player, $playerName) : void {
            if(($key = array_search([$player->getName() => $clickedCave], Main::$request[$playerName])) !== false)
                unset(Main::$request[$playerName][$key]);

        }), 20 * 30);

        $player->sendMessage(MessageUtil::format("§7Poprawnnie wyslales zaproszenie do jaskini, wybrany gracz ma §l§930§r§7 sekund na zaakceptowanie zaproszenia! §8(§9§l" . $clickedCave . "§r§8)"));
        $p->sendMessage(MessageUtil::formatLines(["Gracz o nicku §l§9" . $player->getName(), "§r§7Zaprosil cie do jaskini o tagu§9§l " . $clickedCave . "§7! §8(§l§9/caveblock§r§8)! §7za §l§930 §r§7sekund zaprosznie wygasnie"]));
    }
}