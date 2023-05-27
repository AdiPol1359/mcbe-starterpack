<?php

declare(strict_types=1);

namespace core\forms;

use pocketmine\player\Player;

class Confirmation extends BaseForm {

    public function __construct(string $title, string $content, string $button1, string $button2, private $button1CallBack, private $button2CallBack) {
        $data = [
            "type" => "modal",
            "title" => $title,
            "content" => $content,
            "button1" => $button1,
            "button2" => $button2
        ];

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {
        $data == 1 ? ($this->button1CallBack)() : ($this->button2CallBack)();
    }
}