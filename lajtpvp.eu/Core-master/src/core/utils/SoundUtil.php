<?php

declare(strict_types=1);

namespace core\utils;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Server;
use pocketmine\world\Position;

final class SoundUtil {

    public function __construct() {}

    public static function addSound(array $players, Vector3 $vector, string $sound, int $volume = 100, int $pitch = 1) : void {

        $packet = new PlaySoundPacket();
        $packet->soundName = $sound;
        $packet->x = $vector->getX();
        $packet->y = $vector->getY();
        $packet->z = $vector->getZ();
        $packet->volume = $volume;
        $packet->pitch = $pitch;

        if(empty($players)) {
            BroadcastUtil::broadcastCallback(function($onlinePlayer) use (&$players) : void {
                $players[] = $onlinePlayer;
            });
        }

        Server::getInstance()->broadcastPackets($players, [$packet]);
    }
}