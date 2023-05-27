<?php

declare(strict_types=1);

namespace core\managers\terrain;

use core\Main;
use core\utils\Settings;
use core\utils\VectorUtil;
use JetBrains\PhpStorm\Pure;
use pocketmine\world\Position;

class TerrainManager {

    /** @var Terrain[] */
    private array $terrains = [];

    public function __construct(private Main $plugin) {}

    public function load() : void {
        foreach($this->plugin->getProvider()->getQueryResult("SELECT * FROM terrain", true) as $row) {
            $pos1 = VectorUtil::getPositionFromData($row["pos1"]);
            $pos2 = VectorUtil::getPositionFromData($row["pos2"]);

            $settingsList = [];

            foreach(Settings::$TERRAIN_SETTINGS as $settingName => $translatedName) {
                $settingsList[$settingName] = true;
            }

            $settings = explode(";", $row["settings"]);

            foreach($settings as $setting){
                $resultSetting = explode(":", $setting);

                if(!isset($resultSetting[1]) || !isset($settingsList[$resultSetting[0]])) {
                    continue;
                }

                $settingsList[$resultSetting[0]] = (bool)$resultSetting[1];
            }

            $this->createTerrain($row["name"], (int)$row["priority"], $pos1, $pos2, $settingsList);
        }
    }

    public function save() : void {
        foreach($this->plugin->getProvider()->getQueryResult("SELECT * FROM terrain", true) as $row) {
            if(!$this->terrainExists($row["name"])) {
                $this->plugin->getProvider()->executeQuery("DELETE FROM terrain WHERE name = '{$row['name']}'");
            }
        }

        foreach($this->terrains as $terrainName => $terrain) {

            $settingsList = "";
            $settings = $terrain->getSettings();

            foreach($settings as $settingName => $settingStatus) {
                $settingsList .= $settingName . "=" . intval($settingStatus) . ";";
            }

            if(!empty($this->plugin->getProvider()->getQueryResult("SELECT * FROM terrain WHERE name = '{$terrainName}'", true))) {
                $this->plugin->getProvider()->executeQuery("UPDATE terrain SET priority = '{$terrain->getPriority()}', pos1 = '{$terrain->getPos1()->__toString()}', pos2 = '{$terrain->getPos2()->__toString()}', settings = '$settingsList' WHERE name = '{$terrainName}'");
            } else {
                $this->plugin->getProvider()->executeQuery("INSERT INTO terrain (name, priority, pos1, pos2, settings) VALUES ('{$terrain->getName()}', '{$terrain->getPriority()}', '{$terrain->getPos1()->__toString()}', '{$terrain->getPos2()->__toString()}', '$settingsList')");
            }
        }
    }

    public function createTerrain($name, int $priority, $pos1, $pos2, $settings = []) : void {
        if(empty($settings)) {
            $settings = [];

            foreach(Settings::$TERRAIN_SETTINGS as $settingName => $translatedName) {
                $settings[$settingName] = true;
            }
        }

        $this->terrains[$name] = new Terrain($name, $priority, $pos1, $pos2, $settings);
    }

    public function terrainExists(string $name) : bool {
        return isset($this->terrains[$name]);
    }

    public function getTerrains(): array {
        return $this->terrains;
    }

    public function getTerrainsFromPos(Position $position) : ?array {

        /** @var Terrain[] $terrains */
        $terrains = [];

        foreach($this->terrains as $terrain) {
            if($terrain->contains($position))
                $terrains[] = $terrain;
        }

        return $terrains;
    }

    public function getPriorityTerrain(Position $position) : ?Terrain {

        $cancelled = [];
        $highestPriority = null;

        $terrains = $this->getTerrainsFromPos($position);

        foreach($terrains as $terrain)
            $cancelled[$terrain->getName()] = $terrain->getPriority();

        foreach($cancelled as $terrainName => $priority){
            if($highestPriority === null || $highestPriority < $priority)
                $highestPriority = $priority;
        }

        if(($key = array_search($highestPriority, $cancelled)) !== false) {
            return $this->getTerrainByName($key);
        }

        return null;
    }

    #[Pure] public function getTerrainByName(string $name) : ?Terrain {
        foreach($this->terrains as $terrain) {
            if($terrain->getName() === $name)
                return $terrain;
        }

        return null;
    }

    public function removeTerrain(string $name) : void {
        unset($this->terrains[$name]);
    }
}