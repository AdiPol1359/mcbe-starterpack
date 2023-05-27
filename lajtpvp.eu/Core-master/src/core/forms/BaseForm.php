<?php

declare(strict_types=1);

namespace core\forms;

use pocketmine\form\Form as IForm;
use pocketmine\player\Player;

abstract class BaseForm implements IForm {

    protected array $data = [];

    abstract function handleResponse(Player $player, $data) : void;

    public function jsonSerialize() : array {
        return $this->data;
    }
}