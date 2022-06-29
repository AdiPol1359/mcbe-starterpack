<?php

namespace core\permission\managers;

use pocketmine\Player;
use core\Main;

class NameTagManager {

    public static function updateNameTag(Player $player) : void {
        $nametag = self::getNameTag($player);

        if($nametag != null)
            $player->setNameTag(self::getNameTag($player));
    }

    public static function getNameTag(Player $player) : ?string {
        $group = Main::getGroupManager()->getPlayer($player->getName())->getGroup();

        if($group == null || $group->getNameTag() == null)
            return null;

        return FormatManager::getFormat($player, $group->getNametag());
    }
}