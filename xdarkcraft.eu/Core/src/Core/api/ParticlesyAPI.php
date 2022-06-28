<?php

namespace Core\api;

use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use Core\Main;

class ParticlesyAPI {

    public const PARTICLE_NONE = -1;
    public const PARTICLE_ROAD_CLOUD = 0;
    public const PARTICLE_ROAD_FIRE = 1;
    public const PARTICLE_RINGO = 2;
    public const PARTICLE_CLOUD = 3;

    public const COLOR_NONE = 10;
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

    public static $particles = [
        0 => [],
        1 => [],
        2 => [],
        3 => []
    ];

    public static $lastRainbow = [];

    public static function enableParticle(Player $player, int $type, int $color, bool $disablePrevious = true) : void {
        if($disablePrevious)
            self::disableAllParticles($player, $type);

        self::$particles[$type][] = [$player->getName(), $color];
    }

    public static function disableParticle(Player $player, int $type) : void {
        $nick = $player->getName();

        foreach(self::$particles[$type] as $key => $data)
            if($data[0] == $nick)
                unset(self::$particles[$type][$key]);
    }

    public static function disableAllParticles(Player $player, ?int $type = self::PARTICLE_NONE) : void {
        $nick = $player->getName();

        if($type == self::PARTICLE_NONE) {
            foreach(self::$particles as $type => $datas) {
                foreach($datas as $key => $data)
                    if($data[0] == $nick)
                        unset(self::$particles[$type][$key]);
            }
        } else {
            foreach(self::$particles[$type] as $key => $data) {
                if($data[0] == $nick)
                    unset(self::$particles[$type][$key]);
            }
        }
    }

    public static function hasParticleEnable(Player $player, int $type) : bool {
        $nick = $player->getName();

        foreach(self::$particles[$type] as $data)
            if($data[0] == $nick)
                return true;

    return false;
    }

    private static function getParticleData(Player $player, int $type) : ?array {
        $nick = $player->getName();

        foreach(self::$particles[$type] as $data) {
            if($data[0] == $nick) {
                return $data;
            }
        }

        return null;
    }

    public static function getRGB(Player $player, int $color) : array {
        $nick = $player->getName();

        switch($color) {
            case self::COLOR_GREEN:
                return [85, 255, 85];
            break;

            case self::COLOR_AQUA:
                return [85, 255, 255];
            break;

            case self::COLOR_RED:
                return [255, 85, 85];
            break;

            case self::COLOR_PINK:
                return [255, 85, 255];
            break;

            case self::COLOR_YELLOW:
                return [255, 255, 85];
            break;

            case self::COLOR_WHITE:
                return [255, 255, 255];
            break;

            case self::COLOR_BLACK:
                return [0, 0, 0];
            break;

            case self::COLOR_DARK_BLUE:
                return [0, 0, 170];
            break;

            case self::COLOR_DARK_GREEN:
                return [0, 170, 0];
            break;

            case self::COLOR_DARK_AQUA:
                return [0,170,170];
            break;

            case self::COLOR_DARK_RED:
                return [170, 0, 0];
            break;

            case self::COLOR_DARK_PURPLE:
                return [170, 0, 170];
            break;

            case self::COLOR_GOLD:
                return [255, 170, 0];
            break;

            case self::COLOR_GRAY:
                return [170, 170, 170];
            break;

            case self::COLOR_DARK_GRAY:
                return [85, 85, 85];
            break;

            case self::COLOR_BLUE:
                return [85, 85, 255];
            break;

            case self::COLOR_RAINBOW:
                if(!isset(self::$lastRainbow[$nick]))
                    self::$lastRainbow[$nick] = [255, 0, 0];

                self::rainbow($player);

                return self::$lastRainbow[$nick];
            break;
        }
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