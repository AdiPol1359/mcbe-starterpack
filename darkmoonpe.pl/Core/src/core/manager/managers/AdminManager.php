<?php

namespace core\manager\managers;

use core\Main;
use core\manager\BaseManager;
use pocketmine\Server;

class AdminManager extends BaseManager {

    public static function getAdminsOnline() : array {
        return Main::$adminsOnline;
    }

    public static function sendMessage(string $message, $notAdmin = []) : void {
        foreach(Main::$adminsOnline as $key => $admin) {

            $adminOnline = Server::getInstance()->getPlayerExact($admin);

            if(!$adminOnline) {
                unset(Main::$adminsOnline[$key]);
                continue;
            }

            if(in_array($admin, $notAdmin))
                continue;

            if(!$adminOnline->isConnected())
                return;

            $adminOnline->sendMessage($message);
        }
    }
}