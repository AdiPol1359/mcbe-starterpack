<?php

namespace core\listener\events;

use core\listener\BaseListener;
use core\manager\managers\CpsManager;
use core\Main;
use core\manager\managers\SkinManager;
use core\util\utils\ConfigUtil;
use core\util\utils\SkinUtil;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\NetworkStackLatencyPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;

class PacketReceiveListener extends BaseListener {
    /**
     * @param DataPacketReceiveEvent $e
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function onDataPacketReceive(DataPacketReceiveEvent $e) : void {
        $packet = $e->getPacket();
        $player = $e->getPlayer();
        if($packet instanceof NetworkStackLatencyPacket) {
            if(isset(Main::$callbacks[$id = $player->getId()][$ts = $packet->timestamp])) {
                $cb = Main::$callbacks[$id][$ts];
                unset(Main::$callbacks[$id][$ts]);
                if(count(Main::$callbacks[$id]) === 0) {
                    unset(Main::$callbacks[$id]);
                }
                $cb();
            }
        }
    }

    public function ProtocolDataPacketReceive(DataPacketReceiveEvent $e) {
        $pk = $e->getPacket();
        if($pk instanceof LoginPacket) {
            if(in_array($pk->protocol, ConfigUtil::PROTOCOLS))
                $pk->protocol = ProtocolInfo::CURRENT_PROTOCOL;
        }
    }

    public function removeAttackSound(DataPacketReceiveEvent $e) {
        $packet = $e->getPacket();

        if($packet instanceof LevelSoundEventPacket) {
            if($packet->sound == $packet::SOUND_ATTACK_NODAMAGE || $packet->sound == $packet::SOUND_ATTACK_STRONG)
                $e->setCancelled(true);
        }
    }

    public function onClick(DataPacketReceiveEvent $e) : void {
        $pk = $e->getPacket();
        $player = $e->getPlayer();

        if($pk instanceof InventoryTransactionPacket) {
            if(($pk::NETWORK_ID === InventoryTransactionPacket::NETWORK_ID && $pk->trData instanceof UseItemOnEntityTransactionData) || ($pk::NETWORK_ID === LevelSoundEventPacket::NETWORK_ID && $pk->sound === LevelSoundEventPacket::SOUND_ATTACK_NODAMAGE))
                CpsManager::Click($player);
        }
    }

    /**
     * @param DataPacketReceiveEvent $e
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function onLoginWings(DataPacketReceiveEvent $e) : void {
        $packet = $e->getPacket();

        if($packet instanceof LoginPacket) {
            $data = $packet->clientData;
            $name = $data["ThirdPartyName"];

            if(!$name) {
                $e->setCancelled(true);
                return;
            }

            if($data["PersonaSkin"]) {
                SkinManager::setPlayerDefaultSkin($name);
                return;
            }

            $image = SkinUtil::skinDataToImage(base64_decode($data["SkinData"], true));

            if($image === null || imagesx($image) * imagesy($image) * 4 !== 16384) {
                SkinManager::setPlayerDefaultSkin($name);
                return;
            }

            SkinManager::setPlayerSkinImage($name, $image);
        }
    }
}