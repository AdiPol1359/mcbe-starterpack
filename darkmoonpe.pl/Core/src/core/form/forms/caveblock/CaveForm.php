<?php

namespace core\form\forms\caveblock;

use core\caveblock\Cave;
use core\form\BaseForm;
use core\form\forms\Error;
use pocketmine\Player;

abstract class CaveForm extends BaseForm {

    protected ?Cave $cave;

    public function __construct(?Cave $cave = null) {
        $this->cave = $cave;
    }

    public function getCave() : ?Cave {
        return $this->cave;
    }

    public function handleResponse(Player $player, $data) : void {
        if(!$this->cave){
            $player->sendForm(new Error($player, "Jaskinia o takim tagu nie istnieje!"));
            return;
        }
    }
}