<?php

namespace core\manager\managers;

use core\manager\BaseManager;
use core\user\UserManager;
use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;
use pocketmine\Server;

class SoundManager extends BaseManager {
    public static function addSound(Player $player, Vector3 $vector, string $sound, $volume = 100, int $pitch = 1) : void {

        $userManager = UserManager::getUser($player->getName());
        if(!$userManager->isSettingEnabled(SettingsManager::SOUNDS))
            return;

        $packet = new PlaySoundPacket();
        $packet->soundName = "$sound";
        $packet->x = $vector->getX();
        $packet->y = $vector->getY();
        $packet->z = $vector->getZ();
        $packet->volume = $volume;
        $packet->pitch = $pitch;

        self::getServer()->broadcastPacket([$player], $packet);
    }

    public static function spawnSpecifySound(Vector3 $position, array $players, string $soundName) : void {
        $pk = new AddActorPacket();
        $pk->type = $soundName;
        $pk->entityRuntimeId = Entity::$entityCount++;
        $pk->position = $position;

        Server::getInstance()->broadcastPacket($players, $pk);
    }
}