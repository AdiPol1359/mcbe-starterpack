<?php

declare(strict_types=1);

namespace core\utils;

use core\Main;
use pocketmine\block\Leaves;
use pocketmine\block\VanillaBlocks;
use pocketmine\Server;
use pocketmine\world\Position;

final class RandomUtil {

    private function __construct() {}

    public static function randomDraw(array $drops) {

        $results = [];
        $chances = [];
        $copyDrop = $drops;

        for($i = 0; $i < 100; $i++) {
            $numbers = [];

            for($y = 0; $y < 10; $y++) {
                $items = null;

                foreach($copyDrop as $key => $dropData) {
                    if($items !== null)
                        break;

                    if($dropData["chance"] <= 0) {
                        unset($copyDrop[$key]);
                        continue;
                    }

                    $items = $key;
                    $copyDrop[$key]["chance"] -= 0.1;
                    break;
                }

                $numbers[] = $items;
            }

            $chances[$i] = $numbers;
        }

        $randomChance = mt_rand(0, 999);

        if(isset($chances[intval(round($randomChance / 10))])) {
            foreach($chances as $firstChance => $secondChanceData) {
                foreach($secondChanceData as $dataKey => $items) {
                    if($dataKey === intval(round($randomChance / 1000))) {
                        $item = ($chances[intval(round($randomChance / 10))][$dataKey]);

                        if(isset($item))
                            $results[] = $drops[$item];
                    }
                }
            }
        }

        if(($count = count($results)) > 1)
            $results = $results[mt_rand(0, ($count - 1))];
        else
            $results = $results[0] ?? null;

        return $results ?? null;
    }

    public static function randomTeleport(array $players) : void {

        $x = mt_rand(-Settings::$BORDER_DATA["border"], Settings::$BORDER_DATA["border"]);
        $z = mt_rand(-Settings::$BORDER_DATA["border"], Settings::$BORDER_DATA["border"]);
        $level = Server::getInstance()->getWorldManager()->getDefaultWorld();

        $block = $level->getBlockAt($x, $level->getHighestBlockAt($x, $z), $z);

        for($i = 0; $i < 100; $i++) {
            $blockPos = $block->getPosition();
            if($blockPos->y >= 100 || $blockPos->y === -1 || $block instanceof Leaves || $block->getId() === VanillaBlocks::WATER() || Main::getInstance()->getGuildManager()->getGuildFromPos($block->getPosition()) !== null || sqrt(pow($blockPos->x - $level->getSafeSpawn()->x, 2) + pow($blockPos->z - $level->getSafeSpawn()->z, 2)) < Settings::$SPAWN_PROTECT) {
                $x = mt_rand(-Settings::$BORDER_DATA["border"], Settings::$BORDER_DATA["border"]);
                $z = mt_rand(-Settings::$BORDER_DATA["border"], Settings::$BORDER_DATA["border"]);

                $block = $level->getBlockAt($x, $level->getHighestBlockAt($x, $z), $z);
            } else
                break;
        }

        foreach($players as $player) {
            if(($user = Main::getInstance()->getUserManager()->getUser($player->getName())))
                $user->setLastData(Settings::$SAFE_TELEPORT, (time() + Settings::$SAFE_TELEPORT_TIME), Settings::$TIME_TYPE);

            $player->getEffects()->clear();
            $player->teleport(new Position($x, $level->getHighestBlockAt($x, $z) + 2, $z, $level));
        }
    }

//    public static function getRandomTeleport() : ?Position {
//
//        $position = null;
//        $x = mt_rand(-Settings::$BORDER_DATA["border"], Settings::$BORDER_DATA["border"]);
//        $z = mt_rand(-Settings::$BORDER_DATA["border"], Settings::$BORDER_DATA["border"]);
//        $level = Server::getInstance()->getDefaultLevel();
//
//        $block = $level->getBlockAt($x, $level->getHighestBlockAt($x, $z), $z);
//
//        for($i = 0; $i < 100; $i++) {
//            if($block->y >= 100 || $block->y === -1 || $block->getId() === Block::LEAVES || $block->getId() === Block::WATER || $block->getId() === Block::FLOWING_WATER || Main::getInstance()->getGuildManager()->getGuildFromPos($block) !== null || sqrt(pow($block->x - $level->getSafeSpawn()->x, 2) + pow($block->z - $level->getSafeSpawn()->z, 2)) < Settings::SPAWN_PROTECT) {
//                $x = mt_rand(-Settings::$BORDER_DATA["border"], Settings::$BORDER_DATA["border"]);
//                $z = mt_rand(-Settings::$BORDER_DATA["border"], Settings::$BORDER_DATA["border"]);
//
//                ChunkRegion::onChunkGenerated($level, $x >> 4, $z >> 4, function() use ($level, $x, $z, &$block) {
//                    $block = $level->getBlockAt($x, $level->getHighestBlockAt($x, $z), $z);
//                });
//            } else
//                break;
//        }
//
//        ChunkRegion::onChunkGenerated($level, $block->x >> 4, $block->z >> 4, function() use ($level, $x, $z, &$position) {
//            $position = new Position($x, $level->getHighestBlockAt($x, $z) + 1, $z, $level);
//        });
//
//        return $position;
//    }

    public static function randomIncognitoName($len = 8) : string {
        $word = array_merge(range('a', 'z'), range('A', 'Z'), range('1', '9'));
        shuffle($word);
        return substr(implode($word), 0, $len);
    }
}