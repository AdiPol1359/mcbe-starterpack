<?php

declare(strict_types=1);

namespace core\permissions\managers;

use core\Main;

class NameTagManager {

    public static function getNameTag(string $nick, ?string $fakeName = null) : ?string {
        $group = Main::getInstance()->getPlayerGroupManager()->getPlayer($nick)->getGroup();

        if($group === null || $group->getNameTag() === null)
            return null;

        return FormatManager::getNameTagFormat($nick, ($fakeName !== "" ? $fakeName : $nick), $group->getNametag());
    }
}