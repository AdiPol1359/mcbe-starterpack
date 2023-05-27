<?php

namespace core\managers;

use core\Main;
use core\utils\Settings;
use GdImage;
use JetBrains\PhpStorm\Pure;
use pocketmine\entity\Skin;
use pocketmine\player\Player;

class SkinManager {

    private array $playersSkins = [];
    private string $defaultGeometryName;
    private string $defaultGeometryData;
    
    public function __construct(private Main $plugin) {
        $plugin->saveResource("/default/defaultSkin.png");
        $plugin->saveResource("/default/defaultGeometry.json");

        $this->defaultGeometryName = "geometry.defaultGeometry";
        $this->defaultGeometryData = file_get_contents(Main::getInstance()->getDataFolder() . "/default/defaultGeometry.json");
    }

    public function setPlayerSkinImage(string $name, $resource) : void {
        imagepng($resource, Settings::$PLAYER_SKIN_PATH . $name . ".png");
    }

    public function setPlayerDefaultSkin(string $name, ?string $dir = null, ?string $path = null) : void {
        copy(($dir ? : Main::getInstance()->getDataFolder()) . "/default/defaultSkin.png", ($path ? : Settings::$PLAYER_SKIN_PATH) . $name . ".png");
    }

    public function getDefaultGeometryName() : string {
        return $this->defaultGeometryName;
    }

    public function getDefaultGeometryData() : string {
        return $this->defaultGeometryData;
    }

    public function getPlayerSkinImage(string $name, ?string $path = null) : GdImage|bool {
        $resource = imagecreatefrompng(($path ? : Settings::$PLAYER_SKIN_PATH) . $name . ".png");
        imagecolortransparent($resource, imagecolorallocatealpha($resource, 0, 0, 0, 127));

        return $resource;
    }

    public function setPlayerSkin(Player $player, Skin $skin) : void {
        $this->playersSkins[$player->getName()] = $skin;
    }

    public function removePlayerSkin(Player $player) : void {
        unset($this->playersSkins[$player->getName()]);
    }

    #[Pure] public function getPlayerSkin(Player $player) : ?Skin {
        return $this->playersSkins[$player->getName()] ?? null;
    }
}