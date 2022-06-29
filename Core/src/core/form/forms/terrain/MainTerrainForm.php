<?php

namespace core\form\forms\terrain;

use core\form\BaseForm;
use core\manager\managers\terrain\Terrain;
use pocketmine\Player;

class MainTerrainForm extends BaseForm {

    private Terrain $terrain;

    public function __construct(Terrain $terrain) {

        $data = [
            "type" => "custom_form",
            "title" => "§8Teren: §l§9".$terrain->getName()."§r§8!",
            "content" => []
        ];

        foreach($terrain->getSettings() as $setting => $settingData)
            $data["content"][] = ["type" => "toggle", "text" => "§7".$settingData["name"], "default" => $settingData["status"], "id" => count($data["content"]), "settingName" => $setting];

        $data["content"][] = ["type" => "input", "text" => "§7Wpisz priorytet terenu", "placeholder" => "", "default" => (string)$terrain->getPritority() ?? (string)1, "id" => count($data["content"]), "otherSetting" => "priority"];

        $this->terrain = $terrain;
        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        foreach($this->data["content"] as $index){
            foreach($data as $dataKey => $dataNum) {

                if(isset($index["otherSetting"])){
                    switch($index["otherSetting"]){
                        case "priority":

                            $this->terrain->setPriority($dataNum);
                            break;
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