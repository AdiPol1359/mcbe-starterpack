<?php

namespace core\permission\managers;

use core\Main;

class ChatManager {

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