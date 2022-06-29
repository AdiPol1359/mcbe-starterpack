<?php

namespace core\anticheat;

use core\anticheat\module\BaseModule;
use core\anticheat\module\modules\AntiNoclipModule;
use core\anticheat\module\modules\AntiSpeedMineModule;
use core\anticheat\module\modules\AntiSpeedModule;
use core\anticheat\module\modules\AntiTeleportHackModule;
use core\anticheat\module\modules\data\DistPlayerCalculator;
use core\Main;
use core\manager\BaseManager;

class AntiCheatManager extends BaseManager {

    /** @var BaseModule[] */
    private static array $anticheats = [];

    public static function init() : void {

        $modules = [
            new AntiSpeedMineModule(),
            new AntiSpeedModule(),
            new AntiTeleportHackModule(),
            new AntiNoclipModule(),
            new DistPlayerCalculator()
        ];

        self::$anticheats = $modules;

        foreach($modules as $module)
            self::getServer()->getPluginManager()->registerEvents($module, Main::getInstance());
    }

    public static function getAntiCheatByName(string $name) : ?BaseModule {
        foreach(self::$anticheats as $anticheat) {
            if($anticheat->getModuleName() === $name)
                return $anticheat;
        }

        return null;
    }
}