<?php

namespace core\entity\entities\mobs;

use core\entity\Entitys;
use core\form\forms\quest\MainQuestForm;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Villager extends Entitys {

    const TYPE_ID = 15;
    const HEIGHT = 1.95;

    public function canBeMovedByCurrents() : bool {
        return false;
    }

    public function canBePushed() : bool {
        return false;
    }

    public function onFirstInteract(Player $player, Item $item, Vector3 $clickPos) : bool {
        $player->sendForm(new MainQuestForm($player));
        return parent::onFirstInteract($player, $item, $clickPos);
    }
}