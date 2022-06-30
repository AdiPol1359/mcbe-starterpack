<?php

namespace core\caveblock;

use core\entity\entities\custom\CaveSpawn;
use core\Main;
use core\generator\GeneratorManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use core\util\utils\SkinUtil;
use pocketmine\entity\Entity;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\Server;

class CaveManager {

    /** @var Cave[] */
    private static array $caves = [];

    public static function init() : void {
        Main::getDb()->query("CREATE TABLE IF NOT EXISTS 'cave' (owner TEXT, tag TEXT, x FLOAT, y FLOAT, z FLOAT, v_x FLOAT, v_y FLOAT, v_z FLOAT, f_fire INT, locked INT, b_b_pl INT, b_n_pl INT, b_b_off INT, b_n_off INT, b_b_time INT, b_n_time INT, i_b_time INT, f_time INT, t_time INT)");
        Main::getDb()->query("CREATE TABLE IF NOT EXISTS 'cavepermissions' (nick TEXT, tag TEXT, i_beacon INT, o_chest INT, p_block INT, b_block INT, p_item INT, d_item INT, z_perm INT)");
    }

    public static function saveCaves() : void {

        foreach(self::$caves as $key => $caveSetting){
            $cave = $caveSetting->getName();
            $spawn = $caveSetting->getSpawn();
            $vspawn = $caveSetting->getVillagerSpawn();
            if(empty(Main::getDb()->query("SELECT * FROM 'cave' WHERE tag = '$cave'")->fetchArray())) {
                Main::getDb()->query("INSERT INTO 'cave' (owner, tag, x, y, z, v_x, v_y, v_z, f_fire, locked, b_b_pl, b_n_pl, b_b_off, b_n_off, b_b_time, b_n_time, i_b_time, f_time, t_time) VALUES 
                ('{$caveSetting->getOwner()}', '{$caveSetting->getName()}', '{$spawn->x}', '{$spawn->y}', '{$spawn->z}', '{$vspawn->x}', '{$vspawn->y}', '{$vspawn->z}', '{$caveSetting->getCaveSetting('f_fire')}', '{$caveSetting->isLocked()}', '{$caveSetting->getCaveSetting('b_b_pl')}', '{$caveSetting->getCaveSetting('b_n_pl')}', '{$caveSetting->getCaveSetting('b_b_off')}', '{$caveSetting->getCaveSetting('b_n_off')}', '{$caveSetting->getCaveSetting('b_b_time')}', '{$caveSetting->getCaveSetting('b_n_time')}', '{$caveSetting->getCaveSetting('i_b_time')}', '{$caveSetting->getTimeSetting('f_time')}', '{$caveSetting->getTimeSetting('t_time')}')");
            }else
                Main::getDb()->query("UPDATE 'cave' SET owner = '{$caveSetting->getOwner()}', tag = '{$caveSetting->getName()}', x = '{$spawn->x}', y = '{$spawn->y}', z = '{$spawn->z}', v_x = '{$spawn->x}', v_y = '{$spawn->y}', v_z = '{$spawn->z}', f_fire = '{$caveSetting->getCaveSetting('f_fire')}', locked = '{$caveSetting->isLocked()}', b_b_pl = '{$caveSetting->getCaveSetting('b_b_pl')}', b_n_pl = '{$caveSetting->getCaveSetting('b_n_pl')}', b_b_off = '{$caveSetting->getCaveSetting('b_b_off')}', b_n_off = '{$caveSetting->getCaveSetting('b_n_off')}', b_b_time = '{$caveSetting->getCaveSetting('b_b_time')}', b_n_time = '{$caveSetting->getCaveSetting('b_n_time')}', i_b_time = '{$caveSetting->getCaveSetting('i_b_time')}', f_time = '{$caveSetting->getTimeSetting('f_time')}', t_time = '{$caveSetting->getTimeSetting('t_time')}' WHERE tag = '$cave'");

            foreach($caveSetting->getPlayers() as $player => $setting) {

                $caveManager = self::getCaveByTag($cave);
                if($caveManager->isOwner($player)){
                    if(!$caveManager->getPlayerSetting($player, "z_perm")){
                        $caveManager->switchPlayerSetting($player, "z_perm", 1);
                        Main::getDb()->query("UPDATE 'cavepermissions' SET z_perm = 1 WHERE nick = '{$player}'");
                    }
                }

                if(empty(Main::getDb()->query("SELECT * FROM 'cavepermissions' WHERE tag = '$cave' AND nick = '$player'")->fetchArray())) {
                    Main::getDb()->query("INSERT INTO 'cavepermissions' (nick, tag, i_beacon, o_chest, p_block, b_block, p_item, d_item, z_perm) VALUES
                    ('$player', '$cave', '{$setting['i_beacon']}', '{$setting['o_chest']}', '{$setting['p_block']}', '{$setting['b_block']}', '{$setting['p_item']}', '{$setting['d_item']}', '{$setting['z_perm']}')");
                }else
                    Main::getDb()->query("UPDATE 'cavepermissions' SET nick = '$player', tag = '$cave', i_beacon = '{$setting['i_beacon']}', o_chest = '{$setting['o_chest']}', p_block = '{$setting['p_block']}', b_block = '{$setting['b_block']}', p_item = '{$setting['p_item']}', d_item = '{$setting['d_item']}', z_perm = '{$setting['z_perm']}' WHERE nick = '$player' AND tag = '$cave'");
            }

            $playersFound = Main::getDb()->query("SELECT * FROM 'cavepermissions' WHERE tag = '$cave'");
            while($row = $playersFound->fetchArray()){
                if(!array_key_exists($row["nick"], $caveSetting->getPlayers()))
                    Main::getDb()->query("DELETE FROM 'cavepermissions' WHERE nick = '{$row['nick']}'");
            }
        }

        $cavesFound = Main::getDb()->query("SELECT * FROM 'cave'");
        while($row = $cavesFound->fetchArray()){
            if(!self::existsCave((string)$row["tag"]))
                Main::getDb()->query("DELETE FROM 'cave' WHERE tag = '{$row['tag']}'");
        }
    }

    public static function setDefaultCaves() : void {

        $cave = Main::getDb()->query("SELECT * FROM cave");
        $cavePermission = Main::getDb()->query("SELECT * FROM 'cavepermissions'");
        $caveArray = $cave->fetchArray(SQLITE3_ASSOC);

        if(is_bool($caveArray))
            $caveArray = [];

        $players = [];

        while($row = $cavePermission->fetchArray()) {
            $perms = [];
            foreach($row as $index => $value) {
                if(is_numeric($value))
                    $perms[$index] = $value;
            }

            $players[$row["tag"]][$row["nick"]] = $perms;
        }

        $settings = [];
        $timeSettings = [];

        foreach($caveArray as $row => $setting) {
            $blockNames = ["owner", "tag", "x", "y", "z", "v_x", "v_y", "v_z", "f_time", "t_time"];
            if(!in_array($row, $blockNames))
                $settings[$row] = $setting;
        }

        foreach($caveArray as $row => $setting) {
            if($row === "f_time" || $row === "t_time")
                $timeSettings[$row] = $setting;
        }

        $while = Main::getDb()->query("SELECT * FROM cave");
        while($row = $while->fetchArray(SQLITE3_ASSOC)) {

            $cavePlayers = [];

            foreach($players as $tag => $player) {
                if(strval($tag) === strval($row["tag"]))
                    foreach($player as $nick => $perm)
                        $cavePlayers[$nick] = $perm;
            }

            self::$caves[] = new Cave((string) $row["tag"], $row["owner"], $cavePlayers, new Vector3($row["x"], $row["y"], $row["z"]), new Vector3($row["v_x"], $row["v_y"], $row["v_z"]), $settings, $timeSettings, ConfigUtil::LEVEL . $row["tag"]);
        }
    }

    public static function getCaveByTag(string $tag) : ?Cave{
        foreach(self::$caves as $cave) {
            if($cave->getName() === $tag)
                return $cave;
        }

        return null;
    }

    public static function createCave(Player $player, string $tag, int $f_f = 0, int $lock = 0) : void {

        if(is_dir(Server::getInstance()->getDataPath()."worlds/".ConfigUtil::LEVEL.$tag)){
            $player->sendMessage(MessageUtil::format("Doszlo do bledu podczas tworzenia jaskini, sproboj ponownie pozniej!"));
            return;
        }

        $x = -0.5;
        $y = 47;
        $z = -0.5;
        $vx = -3;
        $vy = 47;
        $vz = 6.5;

        $name = ConfigUtil::LEVEL.$tag;

        GeneratorManager::createWorld($name);

        $level = Server::getInstance()->getLevelByName($name);

        $players[$player->getName()] = ["i_beacon" => 1, "o_chest" => 1, "p_block" => 1, "b_block" => 1, "p_item" => 1, "d_item" => 1, "z_perm" => 1];
        $settings = ["f_fire" => $f_f, "locked" => $lock, "b_b_pl" => 0, "b_n_pl" => 0, "b_b_off" => 0, "b_n_off" => 0, "b_b_time" => 0, "b_n_time" => 0, "i_b_time" => 0];
        $timeSettings = ["f_time" => 0, "t_time" => 0];

        self::$caves[] = new Cave($tag, $player->getName(), $players, new Vector3($x, $y, $z), new Vector3($vx, $vy, $vz), $settings, $timeSettings, $level->getName());

        $nbt = Entity::createBaseNBT(new Position($vx, $vy, $vz), null, 180);

        $villager = Entity::createEntity("Villager", $level, $nbt);
        $villager->setNameTag("§l§9QUEST MASTER");
        $villager->spawnToAll();

        $nbtSpawn = Entity::createBaseNBT((new Position($x, $y, $z, $level))->add(0, 1), null, 0, 0);

        is_file(Main::getInstance()->getDataFolder()."/playersSkins/".$player->getName().".png") ? $skin = SkinUtil::getSkinFromPath(Main::getInstance()->getDataFolder()."/playersSkins/".$player->getName().".png") : $skin = SkinUtil::getSkinFromPath(Main::getInstance()->getDataFolder()."/default/defaultSkin.png");

        $nbtSpawn->setTag(new CompoundTag('Skin', [
            new StringTag('Name', "CaveSpawn"),
            new ByteArrayTag('Data', $skin)
        ]));

        (new CaveSpawn($level, $nbtSpawn))->spawnToAll();
    }

    public static function hasCave(Player $player) : bool {
        return count(self::getCaves($player->getName())) <= 0 ? false : true;
    }

    public static function loadCaves(Player $player) : void {

        $caves = self::getCaves($player->getName());

        foreach($caves as $cave) {
            if(!Server::getInstance()->isLevelLoaded($cave->getLevel()))
                Server::getInstance()->loadLevel($cave->getLevel());
        }
    }

    public static function unLoadCaves(Player $player) : void {

        $caves = self::getCaves($player->getName());

        foreach($caves as $cave) {
            if(Server::getInstance()->isLevelLoaded($cave->getLevel()))
                Server::getInstance()->unloadLevel(Server::getInstance()->getLevelByName($cave->getLevel()));
        }
    }

    public static function caveCount(Player $player) : int {

        $caves = 0;

        foreach(self::$caves as $row => $value) {
            if(array_key_exists($player->getName(), $value->getPlayers()))
                if($value->isOwner($player->getName()))
                    $caves++;
        }

        return $caves;
    }

    public static function getCaves(string $nick) : array {

        /** @var Cave[] $caves */
        $caves = [];

        foreach(self::$caves as $row => $value) {
            if(array_key_exists($nick, $value->getPlayers()))
                $caves[] = $value;
        }

        return $caves;
    }

    public static function existsCave(string $tag) : bool {
        foreach(self::$caves as $row => $value) {
            if($tag === $value->getName())
                return true;
        }

        return false;
    }

    public static function existsCaveExact(string $tag) : bool {
        foreach(self::$caves as $row => $value) {
            if(strtolower($tag) === strtolower($value->getName()))
                return true;
        }

        return false;
    }

    public static function getCountOfRequest(Player $player) : int {
        return count(Main::$request[$player->getName()]);
    }

    public static function isInCave(Player $player) : bool {
        return substr($player->getLevel()->getName(), 0, 10) === ConfigUtil::LEVEL;
    }

    public static function getCave(Player $player) : ?Cave {
        if(!self::isInCave($player))
            return null;

        $lvl = str_replace(ConfigUtil::LEVEL, "", $player->getLevel()->getName());

        if(!self::existsCave($lvl))
            return null;

        return self::getCaveByTag($lvl);
    }

    public static function getMaxPlayerCaves(Player $player) : int {

        $count = ConfigUtil::MAX_PLAYER_CAVES;

        if($player->hasPermission(ConfigUtil::PERMISSION_TAG . "vip.caves"))
            $count = ConfigUtil::MAX_VIP_CAVES;
        if($player->hasPermission(ConfigUtil::PERMISSION_TAG . "svip.caves"))
            $count = ConfigUtil::MAX_SVIP_CAVES;
        if($player->hasPermission(ConfigUtil::PERMISSION_TAG . "sponsor.caves"))
            $count = ConfigUtil::MAX_SPONSOR_CAVES;

        return $count;
    }

    public static function unsetCave(string $tag) : void{
        foreach(self::$caves as $key => $cave) {
            if($cave->getName() === $tag)
                unset(self::$caves[$key]);
        }
    }

    public static function getRegisteredCaves() : int{
        return count(self::$caves ?? []);
    }
}