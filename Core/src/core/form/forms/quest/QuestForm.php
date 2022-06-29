<?php

namespace core\form\forms\quest;

use core\form\BaseForm;
use core\user\UserManager;
use pocketmine\Player;

class QuestForm extends BaseForm {
    public function __construct(Player $player) {
        $data = [
            "type" => "form",
            "title" => "§l§9QUESTY",
            "content" => "",
            "buttons" => []
        ];

        $userManager = UserManager::getUser($player->getName());

        foreach($userManager->getDoneQuests() as $quest)
            $data["buttons"][] = ["text" => "§8§l» §9" . $quest->getCleanName() . " §8§l«§r\n§8Kliknij aby zobaczyc informacje", "id" => count($data["buttons"])];

        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        $userManager = UserManager::getUser($player->getName());

        foreach($userManager->getDoneQuests() as $quest) {
            foreach($this->data["buttons"] as $index)
                if($index["id"] === $data) {
                    if(strpos($index["text"], $quest->getCleanName()))
                        $player->sendForm(new QuestInfoForm($quest->getId()));
                }
        }
    }
}