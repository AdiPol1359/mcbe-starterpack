<?php

namespace Core\api;

use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\ItemFrame;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\entity\{
    Entity, Creature
};
use pocketmine\utils\Config;
use Core\Main;

class ProtectAPI {

    public static $data = [];

    private static $config;

    public const FLAG_BREAK = "break";
    public const FLAG_PLACE = "place";
    public const FLAG_DAMAGE = "damage";
    public const FLAG_INTERACT = "interact";

    public static function init() : void {
        self::$config = new Config(Main::getInstance()->getDataFolder(). 'protect.yml', Config::YAML);
    }

    public static function isTerrainExists(string $name) : bool {
        return self::$config->exists($name);
    }

    public static function createTerrain(string $name, array $pos) : void {
        self::$config->set($name, [
            "pos1" => [
                $pos[0]->getFloorX(),
                $pos[0]->getFloorY(),
                $pos[0]->getFloorZ(),
            ],
            "pos2" => [
                $pos[1]->getFloorX(),
                $pos[1]->getFloorY(),
                $pos[1]->getFloorZ(),
            ],
            "players" => [],
            "flags" => [
                self::FLAG_BREAK => false,
                self::FLAG_PLACE => false,
                self::FLAG_INTERACT => false,
                self::FLAG_DAMAGE => false
            ],
            "whiteblocks" => []
        ]);
        self::$config->save();
    }

    public static function deleteTerrain(string $name) : void {
        self::$config->remove($name);
        self::$config->save();
    }

    public static function getTerrains() : array {
        $terrains = [];

        foreach(self::$config->getAll() as $name => $data)
            $terrains[] = $name;

        return $terrains;
    }

    public static function getTerrainNameFromPos(Vector3 $pos) : ?string {
        $pos = $pos->floor();

        foreach(self::$config->getAll() as $name => $data) {
            $pos1 = new Vector3($data['pos1'][0], $data['pos1'][1], $data['pos1'][2]);
            $pos2 = new Vector3($data['pos2'][0], $data['pos2'][1], $data['pos2'][2]);

            if($pos->getFloorX() <= max($pos1->getFloorX(), $pos2->getFloorX()) && $pos->getFloorX() >= min($pos1->getFloorX(), $pos2->getFloorX()) && $pos->getFloorZ() <= max($pos1->getFloorZ(), $pos2->getFloorZ()) && $pos->getFloorZ() >= min($pos1->getFloorZ(), $pos2->getFloorZ()) && $pos->getFloorY() <= max($pos1->getFloorY(), $pos2->getFloorY()) && $pos->getFloorY() >= min($pos1->getFloorY(), $pos2->getFloorY()))
                return $name;
        }
        return null;
    }

    public static function getFlags(string $name) : array {
        return self::$config->get($name)['flags'];
    }

    public static function setFlag(string $name, string $flag, bool $status = true) : void {
        $terrainData = self::$config->get($name);
        $terrainData['flags'][$flag] = $status;

        self::$config->set($name, $terrainData);
        self::$config->save();
    }

    public static function isFlagExists(string $flag) : bool {
        $flags = [
            self::FLAG_BREAK,
            self::FLAG_PLACE,
            self::FLAG_DAMAGE,
            self::FLAG_INTERACT
        ];

        return in_array($flag, $flags);
    }

    public static function addPlayer(string $name, string $nick) : void {
        $nick = strtolower($nick);
        $terrainData = self::$config->get($name);
        $players = $terrainData['players'];

        if(in_array($nick, $players))
            return;
        $players[] = $nick;

        $terrainData['players'] = $players;

        self::$config->set($name, $terrainData);
        self::$config->save();
    }

    public static function removePlayer(string $name, string $nick) : void {
        $nick = strtolower($nick);
        $terrainData = self::$config->get($name);
        $players = $terrainData['players'];

        if(!in_array($nick, $players))
            return;

        unset($players[array_search($nick, $players)]);

        $newArray = [];

        foreach($players as $player)
            $newArray[] = $player;

        $terrainData['players'] = $newArray;

        self::$config->set($name, $terrainData);
        self::$config->save();
    }

    public static function getPlayers(string $name) : array {
        return self::$config->get($name)['players'];
    }

    public static function canBreak(Player $player, Block $block) : bool {
        if($player->isOp() || $player->hasPermission("PolishHard.protect.break"))
            return true;

        $nick = strtolower($player->getName());

        $terrainName = self::getTerrainNameFromPos($block);

        if($terrainName == null)
            return true;

        $terrainData = self::$config->get($terrainName);

        if(!$terrainData['flags'][self::FLAG_BREAK])
            return true;

        if($player->getInventory()->getItemInHand()->getId() == Item::GOLDEN_PICKAXE)
            return false;

        if(self::isWhiteBlock($terrainName, $block))
            return true;

        if(in_array($nick, $terrainData['players']))
            return true;

        return false;
    }

    public static function canPlace(Player $player, Block $block) : bool {
        if($player->isOp() || $player->hasPermission("PolishHard.protect.EUace"))
            return true;

        $nick = strtolower($player->getName());

        $terrainName = self::getTerrainNameFromPos($block);

        if($terrainName == null)
            return true;

        $terrainData = self::$config->get($terrainName);

        if(!$terrainData['flags'][self::FLAG_PLACE])
            return true;

        if(in_array($nick, $terrainData['players']))
            return true;

        if(in_array($block->getId(), [Block::TNT]))
            return false;

        return false;
    }

    public static function canInteract(Player $player, Block $block) : bool {
        if($player->isOp() || $player->hasPermission("PolishHard.protect.interact"))
            return true;

        $nick = strtolower($player->getName());

        $terrainName = null;

        if(!$block instanceof Air)
            $terrainName = self::getTerrainNameFromPos($block);

        if($terrainName == null) {
            $terrainName = self::getTerrainNameFromPos($player->asVector3());

            if($terrainName == null)
                return true;
        }

        $terrainData = self::$config->get($terrainName);

        if(!$terrainData['flags'][self::FLAG_INTERACT])
            return true;

        if(in_array($nick, $terrainData['players']))
            return true;

        if(in_array($block->getId(), [Block::CHEST, Block::ENDER_CHEST, Block::CRAFTING_TABLE, Block::ENCHANTING_TABLE, Block::ANVIL]))
            return true;

        if($block instanceof ItemFrame)
            return false;

        if(in_array($player->getInventory()->getItemInHand()->getId(), [Item::BOW, Item::ENDER_PEARL, Item::TNT]))
            return false;

        return true;
    }

    public static function canDamage(Entity $entity) : bool {
        if(!$entity instanceof Creature)
            return true;

        if($entity instanceof Player)
            if($entity->isOp() || $entity->hasPermission("PolishHard.protect.damage"))
                return true;

        $nick = strtolower($entity->getName());

        $terrainName = self::getTerrainNameFromPos($entity);

        if($terrainName == null)
            return true;

        $terrainData = self::$config->get($terrainName);

        if(!$terrainData['flags'][self::FLAG_DAMAGE])
            return true;

        if(in_array($nick, $terrainData['players']))
            return true;

        return false;
    }

    public static function canDamageEntity(Entity $entity, Entity $damager) : bool {
        if(!$entity instanceof Creature || !$damager instanceof Creature)
            return true;

        $terrainName = self::getTerrainNameFromPos($damager);

        if($terrainName != null) {
            $terrainData = self::$config->get($terrainName);

            if($terrainData['flags'][self::FLAG_DAMAGE])
                return false;
        }

        $terrainName = self::getTerrainNameFromPos($entity);

        if($terrainName != null) {
            $terrainData = self::$config->get($terrainName);

            if($terrainData['flags'][self::FLAG_DAMAGE])
                return false;
        }

        return true;
    }

    public static function addWhiteBlock(string $name, Block $block) : void {
        $terrainData = self::$config->get($name);
        $whiteblocks = $terrainData['whiteblocks'];

        $pos = [$block->getFloorX(), $block->getFloorY(), $block->getFloorZ()];

        if(in_array($pos, $whiteblocks))
            return;

        $whiteblocks[] = $pos;
        $terrainData['whiteblocks'] = $whiteblocks;

        self::$config->set($name, $terrainData);
        self::$config->save();
    }

    public static function removeWhiteBlock(string $name, Block $block) : void {
        $terrainData = self::$config->get($name);
        $whiteblocks = $terrainData['whiteblocks'];

        $pos = [$block->getFloorX(), $block->getFloorY(), $block->getFloorZ()];

        if(!in_array($pos, $whiteblocks))
            return;

        unset($whiteblocks[array_search($pos, $whiteblocks)]);

        $newArray = [];

        foreach($whiteblocks as $whiteblock)
            $newArray[] = $whiteblock;

        $terrainData['whiteblocks'] = $newArray;

        self::$config->set($name, $terrainData);
        self::$config->save();
    }

    public static function isWhiteBlock(string $name, Block $block) : bool {
        $whiteblocks = self::$config->get($name)['whiteblocks'];
        $pos = [$block->getFloorX(), $block->getFloorY(), $block->getFloorZ()];

        return in_array($pos, $whiteblocks);
    }
}