<?php

namespace core\form\forms\skills;

use core\form\BaseForm;
use core\manager\managers\skill\SkillManager;
use pocketmine\Player;

class SkillShop extends BaseForm {
    public function __construct() {
        $data = [
            "type" => "form",
            "title" => "§l§9SKLEP UMIEJETNOSCI",
            "content" => "",
            "buttons" => []
        ];

        foreach(SkillManager::getSkills() as $skill)
            $data["buttons"][] = ["text" => "§8§l» §9" . $skill->getName() . " §8§l«§r\n§8Kliknij aby zobaczyc"];

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        $i = 0;
        $id = 0;
        $array = SkillManager::getSkills();

        foreach($array as $skill) {
            if($i == intval($data))
                $id = $skill->getId();

            $i++;
        }

        $player->sendForm(new SkillInfo($player, SkillManager::getSkill($id)->getId()));
    }
}