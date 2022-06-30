<?php

namespace core\manager\managers\particle\particles;

use pocketmine\item\Item;
use pocketmine\Player;

interface BaseParticle {

    public static function getName() : string;

    public static function getCost() : float;

    public static function getPlayers() : array;

    public static function onMove() : bool;

    public static function onSpawn(Player $player) : void;

    public static function addPlayer(string $nick) : void;

    public static function removePlayer(string $nick) : void;

    public static function hasPlayer(string $nick) : bool;

    public static function getInventoryItem() : Item;
}