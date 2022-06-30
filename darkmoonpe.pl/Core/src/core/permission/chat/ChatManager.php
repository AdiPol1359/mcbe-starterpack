<?php

namespace core\permission\chat;

use pocketmine\Player;
use core\Main;

class ChatManager {

    public static function getFormat(Player $player, string $message) : string {
        $group = Main::getGroupManager()->getPlayer($player->getName())->getGroup();
        $format = $group->getFormat();

        $format = str_replace("{GROUP}", $group->getName(), $format);
        $format = str_replace("{DISPLAYNAME}", $player->getDisplayName(), $format);
        $format = str_replace("{MESSAGE}", $message, $format);

        return $format;
    }

    public static function setChatPerWorld(bool $status = true) : void {
        $settings = Main::getSettings();

        $settings->set("chat-per-world", $status);
        $settings->save();
    }

    public static function isChatPerWorld() : bool {
        $settings = Main::getSettings();

        return (bool) $settings->get("chat-per-world");
    }
}