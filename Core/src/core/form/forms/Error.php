<?php

namespace core\form\forms;

use core\form\BaseForm;
use core\manager\managers\SoundManager;
use pocketmine\item\ItemIds;
use pocketmine\Player;

class Error extends BaseForm {

    private string $reason;
    private ?BaseForm $back;

    public function __construct(Player $player, string $reason, ?BaseForm $back = null) {

        SoundManager::addSound($player, $player->asVector3(), "block.false_permissions");

        $data = [
            "type" => "form",
            "title" => "§l§9ERROR",
            "content" => "§l§7§k1§r§l§9 ERROR§8: §r§7" . $reason . " §r§l§7§k1",
            "buttons" => []
        ];

        $data["buttons"][] = ["text" => "§8§l» §9Cofnij §8§l«§r\n§8Kliknij aby cofnac", "image" => ["type" => "path", "data" => "textures/blocks/barrier"]];

        $this->reason = $reason;
        $this->data = $data;
        $this->back = $back;

    }

    public function handleResponse(Player $player, $data) : void {
        if(!$this->back !== null)
            $player->sendForm($this->back);
    }
}