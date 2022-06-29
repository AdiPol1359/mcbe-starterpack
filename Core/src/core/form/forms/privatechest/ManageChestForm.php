<?php

namespace core\form\forms\privatechest;

use core\form\BaseForm;
use core\manager\managers\privatechest\ChestManager;
use core\util\utils\MessageUtil;
use pocketmine\block\Block;
use pocketmine\Player;

class ManageChestForm extends BaseForm {

    private array $blocks;

    public function __construct(array $blocks) {

        $owner = "NIKOGO";

        foreach($blocks as $block) {
            if($block->getLevel()->getBlock($block->asVector3())->getId() !== Block::CHEST)
                continue;

            if(($chest = ChestManager::getChest($block->asPosition())) === null)
                continue;

            $owner = $chest->getOwner();
        }

        $data = [
            "type" => "form",
            "title" => "§l§9Odblokowanie skrzynki",
            "content" => "§7Skrzynka nalezy do: §l§9".$owner,
            "buttons" => []
        ];

        $data["buttons"][] = ["text" => "§8§l» §9Odblokuj skrzynke §8§l«§r\n§8Kliknij aby odblokowac"];

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
                    if($player->getLevel()->getBlock($block->asVector3())->getId() !== Block::CHEST)
                        continue;

                    ChestManager::unlockChest($block->asPosition());
                }
                $player->sendMessage(MessageUtil::format("Poprawnie odblokowano skrzynke!"));
                break;
        }
    }
}