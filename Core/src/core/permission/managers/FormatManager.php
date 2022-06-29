<?php

namespace core\permission\managers;

use pocketmine\Player;
use core\Main;

class FormatManager {

    public static function getFormat(Player $player, string $format, ?string $message = null) : string {
        $group = Main::getGroupManager()->getPlayer($player->getName())->getGroup();

        $format = str_replace("&", "§", $format);
        $format = str_replace("{GROUP}", $group->getName(), $format);
        $format = str_replace("{DISPLAYNAME}", $player->getDisplayName(), $format);

        if($message != null)
            $format = str_replace("{MESSAGE}", $message, $format);

        return $format;
    }
}