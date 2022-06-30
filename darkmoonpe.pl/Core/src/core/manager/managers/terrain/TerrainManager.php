<?php

namespace core\manager\managers\terrain;

use core\Main;
use core\manager\BaseManager;
use core\util\utils\VectorUtil;
use pocketmine\level\Position;

class TerrainManager extends BaseManager {

    /** @var Terrain[] */
    private static array $terrains = [];

    public static function init() : void {
        Main::getDb()->query("CREATE TABLE IF NOT EXISTS terrain (name TEXT, priority INT, pos1 TEXT, pos2 TEXT, settings TEXT)");

        $db = Main::getDb()->query("SELECT * FROM terrain");

        while($row = $db->fetchArray(SQLITE3_ASSOC)){
            $pos1 = VectorUtil::getPositionFromData($row["pos1"]);
            $pos2 = VectorUtil::getPositionFromData($row["pos2"]);

            $settingsList = [];

            $settings = explode(";", $row["settings"]);

            foreach($settings as $setting){
                $resultSetting = explode(":", $setting);

                if(!isset($resultSetting[1]))
                    continue;

                $settingsList[$resultSetting[0]] = ["name" => $resultSetting[1], "status" => (bool)$resultSetting[2]];
            }

            self::createTerrain($row["name"], $row["priority"], $pos1, $pos2, $settingsList);
        }
    }

    public static function saveTerrain() : void {

        $db = Main::getDb()->query("SELECT * FROM terrain");

        while($row = $db->fetchArray(SQLITE3_ASSOC)){
            if(!self::terrainExists($row["name"]))
                Main::getDb()->query("DELETE FROM terrain WHERE name = '{$row['name']}'");
        }

        foreach(self::$terrains as $terrainName => $terrain){

            $settingsList = "";
            $settings = $terrain->getSettings();

            foreach($settings as $settingName => $settingData)
                $settingsList .= $settingName . ":" . $settingData["name"] . ":" . intval($settingData["status"]) . ";";

            if(!empty(Main::getDb()->query("SELECT * FROM terrain WHERE name = '{$terrainName}'")->fetchArray()))
                Main::getDb()->query("UPDATE terrain SET priority = '{$terrain->getPritority()}', pos1 = '{$terrain->getPos1()->__toString()}', pos2 = '{$terrain->getPos2()->__toString()}', settings = '$settingsList' WHERE name = '{$terrainName}'");
            else
                Main::getDb()->query("INSERT INTO terrain (name, priority, pos1, pos2, settings) VALUES ('{$terrain->getName()}', '{$terrain->getPritority()}', '{$terrain->getPos1()->__toString()}', '{$terrain->getPos2()->__toString()}', '$settingsList')");
        }
    }

    public static function createTerrain($name, int $priority, $pos1, $pos2, $settings = []) : void {
        self::$terrains[$name] = new Terrain($name, $priority, $pos1, $pos2, $settings);
    }

    public static function terrainExists(string $name) : bool {
        return isset(self::$terrains[$name]);
    }

    public static function getTerrains(): array {
        return self::$terrains;
    }

    public static function getTerrainsFromPos(Position $position) : ?array {

        /** @var Terrain[] $terrains */
        $terrains = [];

        foreach(self::$terrains as $terrain) {
            if($terrain->contains($position))
                $terrains[] = $terrain;
        }

        return $terrains;
    }

    public static function getPriorityTerrain(Position $position) : ?Terrain {

        $cancelled = [];
        $highestPriority = null;

        $terrains = TerrainManager::getTerrainsFromPos($position);

        foreach($terrains as $terrain)
            $cancelled[$terrain->getName()] = $terrain->getPritority();

        foreach($cancelled as $terrainName => $priority){
            if($highestPriority === null || $highestPriority < $priority)
                $highestPriority = $priority;
        }

        if(($key = array_search($highestPriority, $cancelled)) !== false)
            return TerrainManager::getTerrainByName($key);

        return null;
    }

    public static function getTerrainByName(string $name) : ?Terrain {
        foreach(self::$terrains as $terrain) {
            if($terrain->getName() === $name)
                return $terrain;
        }

        return null;
    }

    public static function removeTerrain(string $name) : void {
        unset(self::$terrains[$name]);
    }
}