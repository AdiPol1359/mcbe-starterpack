<?php

namespace core\manager\managers\particle\particles\types;

use core\Main;
use core\manager\managers\particle\particles\BaseParticle;
use core\manager\managers\particle\particles\custom\Dust;
use core\manager\managers\SettingsManager;
use core\user\UserManager;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;

class RingoParticle implements BaseParticle{

    public const COLOR_GREEN = 11;
    public const COLOR_AQUA = 12;
    public const COLOR_RED = 13;
    public const COLOR_PINK = 14;
    public const COLOR_YELLOW = 15;
    public const COLOR_WHITE = 16;
    public const COLOR_BLACK = 17;
    public const COLOR_DARK_BLUE = 18;
    public const COLOR_DARK_GREEN = 19;
    public const COLOR_DARK_AQUA = 20;
    public const COLOR_DARK_RED = 21;
    public const COLOR_DARK_PURPLE = 22;
    public const COLOR_GOLD = 23;
    public const COLOR_GRAY = 24;
    public const COLOR_DARK_GRAY = 25;
    public const COLOR_BLUE = 26;
    public const COLOR_RAINBOW = 27;

    public static array $particles = [
        0 => [],
        1 => [],
        2 => [],
        3 => []
    ];

    private static array $lastRainbow = [];
    private static array $players = [];

    public static function getName() : string{
        return "Ringo";
    }

    public static function getCost() : float{
        return 150;
    }

    public static function addPlayer(string $nick) : void {
        self::$players[] = $nick;
    }

    public static function removePlayer(string $nick) : void {
        if(($key = array_search($nick, self::$players)) !== false)
            unset(self::$players[$key]);
    }

    public static function getPlayers() : array {
        return self::$players;
    }

    public static function onMove() : bool {
        return false;
    }

    public static function hasPlayer(string $nick) : bool{
        (($key = array_search($nick, self::$players)) !== false) ? $bool = true : $bool = false;

        return $bool;
    }

    public static function getInventoryItem() : Item {
        return Item::get(Item::MAGMA_CREAM);
    }

    public static function onSpawn(Player $player) : void{
        if(!$player->isOnGround())
            return;

        $players = [];

        foreach($player->getLevel()->getPlayers() as $onlinePlayer) {
            $user = UserManager::getUser($onlinePlayer->getName());

            if(!$user)
                continue;

            if($user->isSettingEnabled(SettingsManager::PLAYER_PARTICLES))
                $players[] = $onlinePlayer;
        }

        $nick = $player->getName();
        if(!isset(Main::$lastPosition[$nick]['ringo']))
            Main::$lastPosition[$nick]['ringo'] = $player->asPosition();

        $from = Main::$lastPosition[$player->getName()]['ringo'];

        if($player->getX() == $from->getX() && $player->getY() == $from->getY() && $player->getZ() == $from->getZ()) {
            $y = $player->getY() + 0.1;
            $count = 100;

            $rgb = self::getRGB($player, self::COLOR_RAINBOW);
            $particle = new Dust($player->asPosition(), $rgb[0], $rgb[1], $rgb[2], 1);
            for ($yaw = 1, $i = 1; $i <= $count; $yaw += (M_PI * 2) / $count, $i++) {
                $x = -sin($yaw) + $player->x;
                $z = cos($yaw) + $player->z;
                $particle->setComponents($x, $y, $z);
                $player->getLevel()->addParticle($particle, $players);
            }
        } else
            Main::$lastPosition[$nick]['ringo'] = $player->asPosition();
    }

    public static function getRGB(Player $player, int $color) : array {
        $nick = $player->getName();

        switch($color) {
            case self::COLOR_GREEN:
                return [85, 255, 85];

            case self::COLOR_AQUA:
                return [85, 255, 255];

            case self::COLOR_RED:
                return [255, 85, 85];

            case self::COLOR_PINK:
                return [255, 85, 255];

            case self::COLOR_YELLOW:
                return [255, 255, 85];

            case self::COLOR_WHITE:
                return [255, 255, 255];

            case self::COLOR_BLACK:
                return [0, 0, 0];

            case self::COLOR_DARK_BLUE:
                return [0, 0, 170];

            case self::COLOR_DARK_GREEN:
                return [0, 170, 0];

            case self::COLOR_DARK_AQUA:
                return [0,170,170];

            case self::COLOR_DARK_RED:
                return [170, 0, 0];

            case self::COLOR_DARK_PURPLE:
                return [170, 0, 170];

            case self::COLOR_GOLD:
                return [255, 170, 0];

            case self::COLOR_GRAY:
                return [170, 170, 170];

            case self::COLOR_DARK_GRAY:
                return [85, 85, 85];

            case self::COLOR_BLUE:
                return [85, 85, 255];

            case self::COLOR_RAINBOW:
                if(!isset(self::$lastRainbow[$nick]))
                    self::$lastRainbow[$nick] = [255, 0, 0];

                self::rainbow($player);

                return self::$lastRainbow[$nick];
        }

        return [];
    }

    public static function rainbow(Player $player) : void {
        $nick = $player->getName();

        if(!isset(self::$lastRainbow[$nick]))
            self::$lastRainbow[$nick] = [255, 0, 0];

        $rgb = self::$lastRainbow[$nick];

        if($rgb[0] == 255 && $rgb[2] == 0)
            $rgb[1] += 15;

        if($rgb[1] == 255 && $rgb[2] == 0)
            $rgb[0] -= 15;

        if($rgb[0] == 0 && $rgb[1] == 255)
            $rgb[2] += 15;

        if($rgb[0] == 0 && $rgb[2] == 255)
            $rgb[1] -= 15;

        if($rgb[1] == 0 && $rgb[2] == 255)
            $rgb[0] += 15;

        if($rgb[0] == 255 && $rgb[1] == 0)
            $rgb[2] -= 15;

        self::$lastRainbow[$nick] = $rgb;
    }
}