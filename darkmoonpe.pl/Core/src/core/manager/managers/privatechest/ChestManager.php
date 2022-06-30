<?php

namespace core\manager\managers\privatechest;

use core\Main;
use core\util\utils\ConfigUtil;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;

class ChestManager{

    /** @var Chest[] */
    private static array $chests = [];

    public static function init() : void{
        Main::getDb()->query("CREATE TABLE IF NOT EXISTS privatechest (owner TEXT, x INT, y INT, z INT, level TEXT)");
    }

    public static function loadChests() : void {

        $chest = [];
        $db = Main::getDb()->query("SELECT * FROM privatechest");

        while($row = $db->fetchArray(SQLITE3_ASSOC))
            $chest[] = new Chest($row["owner"], new Vector3($row["x"], $row["y"], $row["z"]), $row["level"]);

        self::$chests = $chest;
    }

    public static function saveChests() : void {

        $db = Main::getDb()->query("SELECT * FROM privatechest");

        while($row = $db->fetchArray(SQLITE3_ASSOC)){
            foreach(self::$chests as $index => $value){
                if(!self::getChest(new Position($row["x"], $row["y"], $row["z"], Server::getInstance()->getLevelByName($row["level"]))))
                    Main::getDb()->query("DELETE FROM privatechest WHERE x = '{$row['x']}' AND y = '{$row['y']}' AND z = '{$row['z']}' AND level = '{$row['level']}'");
            }
        }

        foreach(self::$chests as $row => $value) {

            $pos = $value->getChestPosition();
            if(empty(Main::getDb()->query("SELECT * FROM privatechest WHERE owner = '{$value->getOwner()}' AND x = '{$pos->x}' AND y = '{$pos->y}' AND z = '{$pos->z}' AND level = '{$pos->level->getName()}'")->fetchArray()))
                Main::getDb()->query("INSERT INTO privatechest (owner, x, y, z, level) VALUES ('{$value->getOwner()}', '{$pos->x}', '{$pos->y}', '{$pos->z}', '{$pos->level->getName()}')");
        }
    }

    public static function setChest(string $owner, Position $position) : void{
        self::$chests[] = new Chest($owner, $position->asVector3(), $position->level);
    }

    public static function getChest(Position $position) : ?Chest{

        foreach(self::$chests as $index => $chest){
            $chestPosition = $chest->getChestPosition();
            if($chestPosition->equals($position) && $chest->getLevel() === $position->level)
                return $chest;
        }

        return null;
    }

    public static function isLocked(Position $position) : bool{

        foreach(self::$chests as $index => $chest){
            $chestPosition = $chest->getChestPosition();
            if($chestPosition->equals($position) && $chest->getLevel() === $position->level)
                return true;
        }

        return false;
    }

    public static function unlockChest(Position $position) : void{
        foreach(self::$chests as $index => $chest){
            $chestPosition = $chest->getChestPosition();
            if($chestPosition->equals($position) && $chest->getLevel() === $position->level)
                unset(self::$chests[$index]);
        }
    }

    public static function getPlayerChestCount(string $nick) : int{

        $chests = 0;

        foreach(self::$chests as $index => $chest){
            if($chest->getOwner() === $nick)
                $chests++;
        }

        return $chests;
    }

    public static function getMaxLockedChests(Player $player) : int {

        $count = ConfigUtil::MAX_LOCK_CHEST_PLAYER;

        if($player->hasPermission(ConfigUtil::PERMISSION_TAG."privatechest.vip"))
            $count = ConfigUtil::MAX_LOCK_CHEST_VIP;

        if($player->hasPermission(ConfigUtil::PERMISSION_TAG."privatechest.svip"))
            $count = ConfigUtil::MAX_LOCK_CHEST_SVIP;

        if($player->hasPermission(ConfigUtil::PERMISSION_TAG."privatechest.sponsor"))
            $count = ConfigUtil::MAX_LOCK_CHEST_SPONSOR;

        return $count;
    }
}