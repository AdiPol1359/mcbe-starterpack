<?php

declare(strict_types=1);

namespace core\listeners\packet;

use core\managers\nameTag\NameTagPlayerManager;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\SetActorDataPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\StringMetadataProperty;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\player\Player;

class DataPacketSendListener implements Listener {

    public function nameTags(DataPacketSendEvent $e) : void {
        $packets = $e->getPackets();
        $players = $e->getTargets();

        foreach($packets as $packet) {
            foreach($players as $player) {
                if(!$player->getPlayer() instanceof Player) {
                    continue;
                }

                if(!$packet instanceof SetActorDataPacket)
                    return;

                $targetEntity = $player->getPlayer()->getServer()->getWorldManager()->findEntity($packet->actorRuntimeId);
                if (!$targetEntity instanceof Player)
                    return;

                if ($targetEntity->getId() === $player->getPlayer()->getId())
                    return;

                if(!isset($packet->metadata[EntityMetadataProperties::NAMETAG]))
                    return;

                if(($nameTagData = NameTagPlayerManager::getNameTagData($targetEntity->getDisplayName())) === null)
                    return;

                $packet->metadata[EntityMetadataProperties::NAMETAG] = new StringMetadataProperty($nameTagData->nameTagForPlayer($player->getPlayer()));
            }
        }
    }

    public function disableHitSound(DataPacketSendEvent $e) : void {
        $packets = $e->getPackets();

        foreach($packets as $packet) {
            if(!$packet instanceof LevelSoundEventPacket) {
                continue;
            }

            if($packet->sound === LevelSoundEvent::ATTACK_NODAMAGE || $packet->sound === LevelSoundEvent::ATTACK_STRONG) {
                $e->cancel();
            }
        }
    }
}