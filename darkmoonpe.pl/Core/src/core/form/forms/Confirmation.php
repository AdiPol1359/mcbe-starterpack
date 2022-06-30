<?php

namespace core\form\forms;

use core\form\BaseForm;
use pocketmine\Player;

class Confirmation extends BaseForm {

    private $button1CallBack;
    private $button2CallBack;

    public function __construct(string $title, string $content, string $button1, string $button2, callable $button1CallBack, callable $button2CallBack) {
        $data = [
            "type" => "modal",
            "title" => $title,
            "content" => $content,
            "button1" => $button1,
            "button2" => $button2
        ];

        $this->button1CallBack = $button1CallBack;
        $this->button2CallBack = $button2CallBack;

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {
        if($data == 1) {
            $button1 = $this->button1CallBack;
            $button1();
        }else{
            $button2 = $this->button2CallBack;
            $button2();
        }
    }
}