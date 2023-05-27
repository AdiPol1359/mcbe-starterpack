<?php

declare(strict_types=1);

namespace core\managers\wing;

use core\Main;
use core\utils\SkinUtil;
use pocketmine\entity\Skin;
use pocketmine\player\Player;

class WingsManager {

    private array $wings = [];
    private array $wingsNames = [];

    public function __construct(private Main $plugin) {
        $this->load();
    }

    public function load() : void {
        $wingsNames = [];

        $path = Main::getInstance()->getDataFolder() . "wings" . DIRECTORY_SEPARATOR;

        foreach(scandir($path) as $fileName) {
            if(in_array($fileName, [".", ".."]))
                continue;

            if(is_dir($path . $fileName))
                $wingsNames[] = $fileName;
        }

        $this->wingsNames = $wingsNames;

        foreach($wingsNames as $wingName)
            $this->wings[$wingName] = new Wings($wingName, $path);
    }

    public function getWings(string $name) : ?Wings {
        return $this->wings[$name] ?? null;
    }

    public function getWingsNames() : array {
        return $this->wingsNames;
    }

    public function setWings(Player $player, Wings $wings) : void {
        $skin = $player->getSkin();
        $name = $player->getName();

        $skinImage = $this->plugin->getSkinManager()->getPlayerSkinImage($name);

        $linkedImage = $this->linkPlayerAndWingsSkin($skinImage, $wings->getImage());

        $player->setSkin(new Skin($skin->getSkinId(), SkinUtil::skinImageToBytes($linkedImage), "", $wings->getGeometryName(), $wings->getGeometryData()));
        $player->sendSkin();
    }

    public function removeWings(Player $player) : void {
        $player->setSkin($this->plugin->getSkinManager()->getPlayerSkin($player));
        $player->sendSkin();
    }

    public function linkPlayerAndWingsSkin($playerSkin, $wingsSkin) {
        imagecopymerge($wingsSkin, $playerSkin, 0, 0, 0, 0, imagesx($playerSkin), imagesy($playerSkin), 100);

        return $wingsSkin;
    }

    public function getPlayerWings(string $name) : ?Wings {
        $name = strtolower($name);
        $provider = $this->plugin->getProvider();
        $wings = null;

        foreach($provider->getQueryResult("SELECT * FROM wing WHERE player = '".$name."'", true) as $array) {
            if(empty($array))
                return null;

            $wings = $this->getWings($array['wing']);

            if($wings === null) {
                $this->removePlayerWings($name);
                return null;
            }
        }

        return $wings;
    }

    public function hasPlayerWings(string $name) : bool {
        return $this->getPlayerWings($name) !== null;
    }

    public function setPlayerWings(string $name, Wings $wings) : void {
        $name = strtolower($name);
        if($this->hasPlayerWings($name))
            $this->removePlayerWings($name);

        $this->plugin->getProvider()->executeQuery("INSERT INTO wing (player, wing) VALUES ('" . $name . "', '" . $wings->getName() . "')");
    }

    public function removePlayerWings(string $name) : void {
        $name = strtolower($name);
        $this->plugin->getProvider()->executeQuery("DELETE FROM wing WHERE player = '" . $name . "'");
    }
}