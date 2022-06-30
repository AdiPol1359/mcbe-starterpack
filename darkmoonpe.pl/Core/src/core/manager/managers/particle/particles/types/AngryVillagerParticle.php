<?php

namespace core\manager\managers\particle\particles\types;

use core\manager\managers\particle\particles\BaseParticle;
use core\manager\managers\SettingsManager;
use core\user\UserManager;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\level\particle\AngryVillagerParticle as PMAngryVillagerParticle;
use pocketmine\Server;

class AngryVillagerParticle implements BaseParticle {

    private static array $players = [];

    public static function getName() : string {
        return "AngryVillager";
    }

    public static function getCost() : float {
        return 40;
    }

    public static function getPlayers() : array {
        return self::$players;
    }

    public static function onMove() : bool {
        return true;
    }

    public static function getInventoryItem() : Item {
        return Item::get(Item::DYE, 1);
    }

    public static function onSpawn(Player $player) : void {

        $players = [];

        foreach($player->getLevel()->getPlayers() as $onlinePlayer) {
            $user = UserManager::getUser($onlinePlayer->getName());

            if(!$user)
                continue;

            if($user->isSettingEnabled(SettingsManager::PLAYER_PARTICLES))
                $players[] = $onlinePlayer;
        }

        $player->getLevel()->addParticle(new PMAngryVillagerParticle($player->asPosition()), $players);
    }

    public static function hasPlayer(string $nick) : bool{
        (($key = array_search($nick, self::$players)) !== false) ? $bool = true : $bool = false;

        return $bool;
    }

    public static function addPlayer(string $nick) : void {
        self::$players[] = $nick;
    }

    public static function removePlayer(string $nick) : void {
        if(($key = array_search($nick, self::$players)) !== false)
            unset(self::$players[$key]);
    }
}