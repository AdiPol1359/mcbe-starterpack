<?php

namespace core\form;

use pocketmine\form\Form as IForm;
use pocketmine\Player;

abstract class BaseForm implements IForm {

    protected array $data = [];

    abstract function handleResponse(Player $player, $data) : void;

    public function jsonSerialize() : array {
        return $this->data;
    }
}