<?php

namespace core\listeners\player;

use core\utils\MessageUtil;
use core\utils\Settings;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\item\ItemIds;

class PlayerItemConsumeListener implements Listener {

    public function onConsume(PlayerItemConsumeEvent $e) : void {
        $player = $e->getPlayer();
        $item = $e->getItem();

        if($item->getId() !== ItemIds::GOLDEN_APPLE && $item->getId() !== ItemIds::ENCHANTED_GOLDEN_APPLE)
            return;

        $position = $player->getPosition();
        $x = $position->getFloorX();
        $z = $position->getFloorZ();

        $border = Settings::$BORDER_DATA["border"];

        if(abs($x) >= $border || abs($z) >= $border && Settings::$BORDER_DATA["damage"]) {
            $e->cancel();
            $player->sendMessage(MessageUtil::format("Nie mozna jesc za borderem mapy!"));
        }
    }
}