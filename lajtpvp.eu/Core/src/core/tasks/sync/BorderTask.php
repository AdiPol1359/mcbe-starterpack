<?php

declare(strict_types=1);

namespace core\tasks\sync;

use core\managers\BorderPlayerManager;
use core\utils\Settings;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\scheduler\Task;

class BorderTask extends Task {

    public function onRun() : void {

        if(Settings::$BORDER_DATA["damage"]) {
            foreach(BorderPlayerManager::getPlayers() as $key => $onlinePlayer) {
                $position = $onlinePlayer->getPosition();

                $x = $position->getFloorX();
                $z = $position->getFloorZ();

                $border = Settings::$BORDER_DATA["border"];

                if(abs($x) >= ($border + 2) || abs($z) >= ($border + 2))
                    $onlinePlayer->attack(new EntityDamageEvent($onlinePlayer, EntityDamageEvent::CAUSE_ENTITY_ATTACK, 15));
            }
        }
    }
}