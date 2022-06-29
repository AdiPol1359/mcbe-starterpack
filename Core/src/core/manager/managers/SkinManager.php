<?php

namespace core\manager\managers;

use core\Main;
use core\manager\BaseManager;
use pocketmine\entity\Skin;
use pocketmine\Player;

class SkinManager extends BaseManager {

    private static array $playersSkins = [];
    private static $playersSkinsPath;
    private static string $defaultGeometryName;
    private static string $defaultGeometryData;

    public static function init() : void {
        Main::getInstance()->saveResource("/default/defaultSkin.png");
        Main::getInstance()->saveResource("/default/defaultGeometry.json");
        self::$playersSkinsPath = Main::getInstance()->getDataFolder() . "/playersSkins/" . DIRECTORY_SEPARATOR;
        self::$defaultGeometryName = "geometry.defaultGeometry";
        self::$defaultGeometryData = file_get_contents(Main::getInstance()->getDataFolder() . "/default/defaultGeometry.json");
    }

    public static function setPlayerSkinImage(string $name, $resource) : void {
        imagepng($resource, self::$playersSkinsPath . $name . ".png");
    }

    public static function setPlayerDefaultSkin(string $name) : void {
        copy(Main::getInstance()->getDataFolder() . "/default/defaultSkin.png", self::$playersSkinsPath . $name . ".png");
    }

    public static function getDefaultGeometryName() : string {
        return self::$defaultGeometryName;
    }

    public static function getDefaultGeometryData() : string {
        return self::$defaultGeometryData;
    }

    public static function getPlayerSkinImage(string $name) {
        if(!is_file(self::$playersSkinsPath . $name . ".png"))
            self::setPlayerDefaultSkin($name);

        $resource = imagecreatefrompng(self::$playersSkinsPath . $name . ".png");
        imagecolortransparent($resource, imagecolorallocatealpha($resource, 0, 0, 0, 127));

        return $resource;
    }

    public static function setPlayerSkin(Player $player, Skin $skin) : void {
        self::$playersSkins[$player->getName()] = $skin;
    }

    public static function removePlayerSkin(Player $player) : void {
        unset(self::$playersSkins[$player->getName()]);
    }

    public static function getPlayerSkin(Player $player) : ?Skin {
        return self::$playersSkins[$player->getName()] ?? null;
    }
}