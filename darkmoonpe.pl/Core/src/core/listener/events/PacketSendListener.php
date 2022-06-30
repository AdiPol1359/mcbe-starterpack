<?php

namespace core\listener\events;

use core\listener\BaseListener;
use core\Main;
use core\manager\managers\SettingsManager;
use core\network\packets\Images;
use core\user\UserManager;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\NetworkBinaryStream;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\scheduler\ClosureTask;

class PacketSendListener extends BaseListener {
    /**
     * @param DataPacketSendEvent $e
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function onDataPacketSend(DataPacketSendEvent $e) : void {
        $packet = $e->getPacket();
        $player = $e->getPlayer();
        if(!$packet instanceof ModalFormRequestPacket)
            return;

        Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function() use ($player) : void {
            if(!$player->isOnline())
                return;

            Images::PacketSend($player, function() use ($player) : void {
                if(!$player->isOnline())
                    return;

                Images::reQuestManager($player);
                if(5 > 1) {
                    $times = 5 - 1;
                    $handler = null;
                    $handler = Main::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function() use ($player, $times, &$handler) : void {
                        if(--$times >= 0 && $player->isOnline()) {
                            Images::reQuestManager($player);
                        } else {
                            if($handler != null)
                                $handler->cancel();
                            else
                                $handler = null;
                        }
                    }), 10);
                }
            });
        }), 1);
    }

    public function blockParticle(DataPacketSendEvent $e) : void{
        
        $packet = $e->getPacket();
        
        if (!$packet instanceof BatchPacket)
            return;

        $player = $e->getPlayer();

        $user = UserManager::getUser($player->getName());

        if(!$user)
            return;

        if($user->isSettingEnabled(SettingsManager::BLOCK_PARTICLE))
            return;

        $packet->decode();

        $stream = new NetworkBinaryStream($packet->payload);
 		$count = 0;
 		while(!$stream->feof()) {
            if($count++ >= 500)
                return;
        }

        foreach ($packet->getPackets() as $payload) {

            $pk = PacketPool::getPacket($payload);
            $pk->decode();

            if (!$pk instanceof LevelEventPacket)
                continue;

            if (!($pk->evid === LevelEventPacket::EVENT_PARTICLE_DESTROY || $pk->evid === LevelEventPacket::EVENT_PARTICLE_PUNCH_BLOCK))
                continue;

            $e->setCancelled();

            $newBatch = new BatchPacket();
            foreach ($packet->getPackets() as $newPayload) {
                $newPacket = PacketPool::getPacket($newPayload);
                $newPacket->decode();

                if ($newPacket instanceof LevelEventPacket && ($newPacket->evid === LevelEventPacket::EVENT_PARTICLE_DESTROY || $newPacket->evid === LevelEventPacket::EVENT_PARTICLE_PUNCH_BLOCK))
                    continue;

                $newBatch->addPacket($newPacket);
            }

            $newBatch->encode();
            $player->sendDataPacket($newBatch);
            return;
        }
    }

    public function rainPacket(DataPacketSendEvent $e) : void {
        $packet = $e->getPacket();

        if(!$packet instanceof LevelEventPacket)
            return;

        if($packet->evid === LevelEventPacket::EVENT_START_RAIN) {
            $packet->evid = LevelEventPacket::EVENT_STOP_RAIN;
            $packet->encode();
            $e->getPlayer()->dataPacket($packet);
        }
    }
}