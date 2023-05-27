<?php

declare(strict_types=1);

namespace core\listeners\packet;

use core\inventories\FakeInventoryManager;
use core\Main;
use core\manager\CpsManager;
use core\utils\PermissionUtil;
use core\utils\Settings;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\PlayStatusPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;

class DataPacketReceiveListener implements Listener {

    /**
     * @param DataPacketReceiveEvent $e
     * @ignoreCancelled true
     */
    public function fakeInventory(DataPacketReceiveEvent $e) : void {
        $player = $e->getOrigin()->getPlayer();
        $packet = $e->getPacket();

        if($packet instanceof ContainerClosePacket) {
            if(($fakeInventory = FakeInventoryManager::getInventory($player->getName())) !== null) {
                Main::getInstance()->getScheduler()->scheduleTask(new ClosureTask(function() use ($player, $fakeInventory) : void {
                    if($fakeInventory->hasChanged() && $fakeInventory->isClosed()) {
                        $fakeInventory->nextInventory->openFor([$player]);
                    }
                }));
            }
        }
    }

    // HACK: disconnect message is working now!
    public function check(DataPacketSendEvent $e) : void {
        foreach($e->getPackets() as $packet) {
            if(!$packet instanceof PlayStatusPacket || $packet->status !== PlayStatusPacket::LOGIN_SUCCESS) {
                continue;
            }

            foreach($e->getTargets() as $target) {
                // WHITELIST

                if($target->getIp() !== "51.83.137.50" && $target->getIp() !== "127.0.0.1" && $target->getIp() !== "192.168.1.65") {
                    $target->disconnect("§cMUSISZ POLACZYC SIE PRZEZ LOBBY!");
                }

                $whitelistManager = Main::getInstance()->getWhitelistManager();

                if($whitelistManager->isWhitelistEnabled() && !$whitelistManager->isInWhitelist($target->getDisplayName()) && !Server::getInstance()->isOp($target->getDisplayName()) && !PermissionUtil::hasOfflinePlayer($target->getDisplayName(), Settings::$PERMISSION_TAG . "whitelist")) {
                    $target->disconnect(Settings::$WHITELIST_MESSAGE);
                }

                // BAN
                if(($ban = Main::getInstance()->getBanManager()->getBanInfo($target->getDisplayName(), $target->getIp(), $target->getPlayerInfo()->getExtraData()["DeviceId"])) !== null) {
                    $target->disconnect(Main::getInstance()->getBanManager()->getBanFormat($ban));
                }
            }
        }
    }

    public function cpsLimit(DataPacketReceiveEvent $e) : void {
        $pk = $e->getPacket();
        $network = $e->getOrigin();

        if($pk instanceof InventoryTransactionPacket) {
            if(($pk::NETWORK_ID === InventoryTransactionPacket::NETWORK_ID && $pk->trData instanceof UseItemOnEntityTransactionData) || ($pk::NETWORK_ID === LevelSoundEventPacket::NETWORK_ID && $pk->sound === LevelSoundEvent::ATTACK_NODAMAGE))
                Main::getInstance()->getCpsManager()->Click($network->getPlayer());
        }
    }
}