<?php

declare(strict_types=1);

namespace core\forms;

use core\managers\terrain\Terrain;
use JetBrains\PhpStorm\Pure;
use pocketmine\player\Player;

class MainTerrainForm extends BaseForm {

    private Terrain $terrain;

    #[Pure] public function __construct(Terrain $terrain) {

        $data = [
            "type" => "custom_form",
            "title" => "§8Teren: §l§e".$terrain->getName()."§r§8!",
            "content" => []
        ];

        foreach($terrain->getSettings() as $setting => $settingData)
            $data["content"][] = ["type" => "toggle", "text" => "§7".$settingData["name"], "default" => (bool)$settingData["status"], "id" => count($data["content"]), "settingName" => $setting];

        $data["content"][] = ["type" => "input", "text" => "§7Wpisz priorytet terenu", "placeholder" => "", "default" => (string)$terrain->getPriority() ?? "1", "id" => count($data["content"]), "otherSetting" => "priority"];

        $this->terrain = $terrain;
        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        foreach($this->data["content"] as $index){
            foreach($data as $dataKey => $dataNum) {

                if(isset($index["otherSetting"])){
                    if($index["otherSetting"] == "priority") {
                        $this->terrain->setPriority($dataNum);
                    }
                }

                if($index["id"] === $dataKey) {
                    if(!isset($index["settingName"]))
                        continue;

                    $this->terrain->switchSetting($index["settingName"], (bool) $dataNum);
                }
            }
        }
    }
}