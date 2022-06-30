<?php

namespace core\listener;

use core\Main;
use core\manager\BaseManager;

class ListenerManager extends BaseManager {

    public static function registerEvents() : void {
        $listeners = [];

        foreach(scandir(__DIR__ . '/events') as $files) {
            if(!strpos($files, ".php"))
                continue;

            $fileName = __NAMESPACE__ . '\events\\' . str_replace(".php", "", $files);
            $class = new $fileName;
            $listeners[] = $class;
        }

        foreach($listeners as $listener)
            self::getServer()->getPluginManager()->registerEvents($listener, Main::getInstance());
    }
}