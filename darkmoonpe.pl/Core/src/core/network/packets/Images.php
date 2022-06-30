<?php

namespace core\network\packets;

use Closure;
use core\Main;
use pocketmine\{
    entity\Attribute,
    network\mcpe\protocol\NetworkStackLatencyPacket,
    network\mcpe\protocol\UpdateAttributesPacket,
    Player
};

class Images {

    public static function PacketSend(Player $player, Closure $callback) : void {
        $ts = mt_rand() * 1000;
        $pk = new NetworkStackLatencyPacket();
        $pk->timestamp = $ts;
        $pk->needResponse = true;
        $player->sendDataPacket($pk);
        Main::$callbacks[$player->getId()][$ts] = $callback;
    }

    public static function reQuestManager(Player $player) : void {
        $pk = new UpdateAttributesPacket();
        $pk->entityRuntimeId = $player->getId();
        $pk->entries[] = $player->getAttributeMap()->getAttribute(Attribute::EXPERIENCE_LEVEL);
        $player->sendDataPacket($pk);
    }
}