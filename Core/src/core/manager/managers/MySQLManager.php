<?php

namespace core\manager\managers;

use core\manager\BaseManager;
use core\user\UserManager;
use Exception;
use mysqli;
use pocketmine\utils\MainLogger;

class MySQLManager extends BaseManager {

    /**
    public const MYSQL_HOST = "127.0.0.1";
    public const MYSQL_USER = "root";
    public const MYSQL_PASSWORD = "Kobagtpl123";
    public const MYSQL_DB = "darkmoonpe";
     */

    public const MYSQL_HOST = "localhost";
    public const MYSQL_USER = "root";
    public const MYSQL_PASSWORD = "";
    public const MYSQL_DB = "darkmoonpe";

    private static ?mysqli $mysql = null;

    public static function init() : void{

        try {
            self::$mysql = new mysqli(self::MYSQL_HOST, self::MYSQL_USER, self::MYSQL_PASSWORD, self::MYSQL_DB, 3306);
        }catch(Exception $err){
            self::$mysql = null;
            MainLogger::getLogger()->critical("MySQL Error");
        }

        if (mysqli_connect_errno() != 0)
            MainLogger::getLogger()->critical("MySQL Error");

        else {
            MainLogger::getLogger()->warning("Successfully connected to MySQL!");

            self::$mysql->query("CREATE TABLE IF NOT EXISTS topKills (nick TEXT, count FLOAT)");
            self::$mysql->query("CREATE TABLE IF NOT EXISTS topDeaths (nick TEXT, count FLOAT)");
            self::$mysql->query("CREATE TABLE IF NOT EXISTS topSpendTime (nick TEXT, count FLOAT)");
            self::$mysql->query("CREATE TABLE IF NOT EXISTS topAssists (nick TEXT, count FLOAT)");
            self::$mysql->query("CREATE TABLE IF NOT EXISTS topMadeQuests (nick TEXT, count FLOAT)");

            self::$mysql->query("CREATE TABLE IF NOT EXISTS topCobblestone (nick TEXT, count INT)");
            self::$mysql->query("CREATE TABLE IF NOT EXISTS topMoney (nick TEXT, count FLOAT)");
            self::$mysql->query("CREATE TABLE IF NOT EXISTS serverInfo (registerPlayers INT, registerCaves INT)");
        }
    }

    public static function setPlayers() : void{

        if(!self::isConnected())
            return;

        $money = self::$mysql->query("SELECT * FROM topMoney");

        while($row = $money->fetch_array()){
            if(!UserManager::userExists($row["nick"]))
                self::$mysql->query("DELETE FROM topMoney WHERE `nick` = '{$row['nick']}'");
        }

        $cobblestone = self::$mysql->query("SELECT * FROM topCobblestone");

        while($row = $cobblestone->fetch_array()){
            if(!UserManager::userExists($row["nick"]))
                self::$mysql->query("DELETE FROM topCobblestone WHERE `nick` = '{$row['nick']}'");
        }
    }

    public static function isConnected() : bool{
        return self::$mysql ? true : false;
    }

    public static function getMySQL() : ?mysqli{
        return self::$mysql;
    }
}