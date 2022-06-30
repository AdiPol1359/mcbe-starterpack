<?php

namespace core\form\forms\quest;

use core\form\BaseForm;
use core\manager\managers\quest\QuestManager;
use pocketmine\Player;

class QuestInfoForm extends BaseForm {
    public function __construct(int $questId) {

        $quest = QuestManager::getSelectedQuest($questId);

        $data = [
            "type" => "form",
            "title" => "§l§9" . $quest->getCleanName(),
            "content" => "",
            "buttons" => []
        ];

        $data["content"] = "§l§9Stan§r§7: §l§aWYKONANY§r" . "\n" . "§l§9Status§r§7: §l§8(§7" . $quest->getMaxTimes() . "§8/§7" . $quest->getMaxTimes() . "§8)§r" . "\n" . "§l§9Nagroda§r§7: §l" . $quest->getRewardName() . "\n" . "§l§9Osoby z wykonanym questem§r§7: §l" . QuestManager::getMadeCount($questId);
        $data["buttons"][] = ["text" => "§8§l» §9Cofnij §8§l«§r\n§8Kliknij aby cofnac", "image" => ["type" => "path", "data" => "textures/blocks/barrier"]];

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        switch($data) {
            case "0":
                $player->sendForm(new QuestForm($player));
                break;
        }
    }
}