<?php

namespace core\form\forms\skills;

use core\form\BaseForm;
use core\manager\managers\skill\SkillManager;
use core\user\UserManager;
use pocketmine\Player;

class SkillMainForm extends BaseForm {
    public function __construct(Player $player) {

        $data = [
            "type" => "form",
            "title" => "§l§9UMIEJETNOSCI",
            "content" => "",
            "buttons" => []
        ];

        $userManager = UserManager::getUser($player->getName());
        $skills = $userManager->getSkills();
        $names = [];

        foreach($skills as $skill)
            $names[] = SkillManager::getSkill($skill)->getName();

        if(!empty($names)) {
            $resultSkill = implode("\n§8§l»§r§7 ", $names);
            $data["content"] = "§7Twoje umiejetnosci: \n§8§l»§r§7 $resultSkill";
        } else
            $data["content"] = "§7Brak umiejetnosci!";

        $data["buttons"][] = ["text" => "§8§l» §9SKLEP §8§l«§r\n§8Kliknij aby otworzyc sklep"];

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        if($data === 0)
            $player->sendForm(new SkillShop());
    }
}