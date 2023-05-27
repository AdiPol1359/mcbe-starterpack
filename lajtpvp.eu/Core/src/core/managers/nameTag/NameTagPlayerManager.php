<?php

namespace core\managers\nameTag;

use JetBrains\PhpStorm\Pure;
use pocketmine\player\Player;

class NameTagPlayerManager {

    /** @var NameTagPlayer[] */
    private static array $nameTags = [];

    public static function createNameTagData(string $nick) : void {
        self::$nameTags[] = new NameTagPlayer($nick);
    }

    #[Pure] public static function getNameTagData(string $nick) : ?NameTagPlayer {
        foreach(self::$nameTags as $nameTag) {
            if($nameTag->getName() === $nick)
                return $nameTag;
        }

        return null;
    }

    public static function updatePlayersAround(Player $player, bool $owner = true) : void {
        $players = [];

        foreach($player->getViewers() as $viewer)
            $players[] = $viewer;

        if($owner)
            $players[] = $player;

        foreach($players as $onlinePlayer) {
            if(($nameTag = self::getNameTagData($onlinePlayer->getName())))
                $nameTag->updateNameTag();
        }
    }
}