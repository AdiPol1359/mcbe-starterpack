<?php

namespace core\form\forms\quest;

use core\form\BaseForm;
use core\form\forms\Error;
use core\manager\managers\quest\QuestManager;
use core\user\UserManager;
use core\util\utils\MessageUtil;
use pocketmine\Player;

class ChooseQuestForm extends BaseForm {

    public function __construct(Player $player) {
        $data = [
            "type" => "form",
            "title" => "§l§9WYBIERZ QUESTA",
            "content" => "",
            "buttons" => []
        ];

        $user = UserManager::getUser($player->getName());

        foreach($user->getQuests() as $quest => $status) {

            $status = $status ? "§l§aWYKONANY" : "§l§cNIE WYKONANY";

            if($selectedQuest = $user->getSelectedQuest()) {

                if($selectedQuest->getId() === $quest)
                    $status = "§l§6WYKONUJESZ";
            }

            $data["buttons"][] = ["text" => "§r§8" . ($quest = QuestManager::getQuest($quest))->getCleanName() . "\n" . $status, "id" => count($data["buttons"]), "questId" => $quest->getId()];
        }

        $data["buttons"][] = ["text" => "§8§l» §9Cofnij §8§l«§r\n§8Kliknij aby cofnac", "image" => ["type" => "path", "data" => "textures/blocks/barrier"], "id" => count($data["buttons"])];

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        $user = UserManager::getUser($player->getName());

        foreach($this->data["buttons"] as $index) {
            if($index["id"] === $data) {

                if(!isset($index["questId"])) {
                    $player->sendForm(new MainQuestForm($player));
                    return;
                }

                if($user->isSelectedQuest()) {
                    $player->sendForm(new Error($player, "Wykonujesz juz jednego questa!", $this));
                    return;
                }

                if($user->hasMadeSpecifyQuest($index["questId"])) {
                    $player->sendForm(new Error($player, "Juz wykonales tego questa!", $this));
                    return;
                }

                $user->setSelectQuest($index["questId"]);
                $player->sendMessage(MessageUtil::format("Poprawnie wybrales nowego questa!"));
                QuestManager::update($player);
                return;
            }
        }
    }
}