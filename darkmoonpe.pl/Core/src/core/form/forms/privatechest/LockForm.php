<?php

namespace core\form\forms\privatechest;

use core\form\BaseForm;
use core\manager\managers\privatechest\ChestManager;
use core\util\utils\MessageUtil;
use pocketmine\Player;

class LockForm extends BaseForm {

    private array $blocks;

    public function __construct(array $blocks) {
        $data = [
            "type" => "form",
            "title" => "§l§9Blokowanie skrzynki",
            "content" => "",
            "buttons" => []
        ];

        $data["buttons"][] = ["text" => "§8§l» §9Zablokuj skrzynke §8§l«§r\n§8Kliknij aby zablokowac"];

        $this->data = $data;
        $this->blocks = $blocks;
    }

    public function handleResponse(Player $player, $data) : void {

        $formData = json_decode($data);

        if($formData === null)
            return;

        switch($formData){
            case 0:
                foreach($this->blocks as $block) {
                    if(!$player->getLevel()->getBlock($block->asVector3()))
                        continue;

                    ChestManager::setChest($player->getName(), $block->asPosition());
                }

                $player->sendMessage(MessageUtil::format("Poprawnie zablokowano skrzynke!"));
                break;
        }
    }
}