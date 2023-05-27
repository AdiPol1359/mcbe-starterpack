<?php

declare(strict_types=1);

namespace core\listeners\player;

use core\entities\object\PrimedTNT;
use core\inventories\fakeinventories\SafeInventory;
use core\items\custom\Crowbar;
use core\items\custom\Safe;
use core\items\custom\ThrownTNT;
use core\Main;
use core\utils\MessageUtil;
use core\utils\Settings;
use core\utils\SoundUtil;
use core\utils\TimeUtil;
use pocketmine\entity\Location;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\nbt\tag\CompoundTag;

class PlayerItemUseListener implements Listener {

    /**
     * @param PlayerItemUseEvent $e
     * @priority LOW
     * @ignoreCancelled true
     */
    public function protectInteract(PlayerItemUseEvent $e) : void {
        $player = $e->getPlayer();

        if($player->getServer()->isOp($player->getName())) {
            return;
        }

        if(($terrain = Main::getInstance()->getTerrainManager()->getPriorityTerrain($player->getPosition())) !== null){
            if(!$terrain->isSettingEnabled(Settings::$TERRAIN_INTERACT)) {
                $e->cancel();
            }
        }
    }

    /**
     * @param PlayerItemUseEvent $e
     * @priority LOW
     * @ignoreCancelled true
     */
    public function guildInteract(PlayerItemUseEvent $e) : void {
        $player = $e->getPlayer();

        if($player->getServer()->isOp($player->getName()))
            return;
        
        if(($guild = Main::getInstance()->getGuildManager()->getGuildFromPos($player->getPosition())) !== null){
            if(!$guild->existsPlayer($player->getName())) {
                $e->cancel();
            }
        }
    }
    
    public function throwableTnt(PlayerItemUseEvent $e) {
        if($e->isCancelled())
            return;

        $player = $e->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        $user = Main::getInstance()->getUserManager()->getUser($player->getName());

        if($item->equals(new ThrownTNT())) {
            if($user->hasLastData(Settings::$THROWN_TNT)) {
                $time = $user->getLastData(Settings::$THROWN_TNT)["value"];

                if($time > time()) {
                    $player->sendMessage(MessageUtil::format("Rzucaka bedziesz mogl polozyc za §e".TimeUtil::convertIntToStringTime(($time - time()), "§e")));
                    return;
                }
            }

            $e->cancel();

            $item->setCount(1);

            $player->getInventory()->removeItem($item);

            $location = Location::fromObject($player->getEyePos(), $player->getWorld(), 0, 0);
            $motion = $player->getDirectionVector()->multiply(0.6);
            $nbt = CompoundTag::create()
                ->setInt("thrownTnt", 1);

            $entity = new PrimedTNT($location, $nbt);
            if($entity->isClosed())
                return;

            $entity->setMotion($motion);
            $entity->setOwningEntity($player);

            $user->setLastData(Settings::$THROWN_TNT, (time() + Settings::$THROWN_TNT_TIME), Settings::$TIME_TYPE);
        }
    }

    public function openSafe(PlayerItemUseEvent $e) : void {
        $player = $e->getPlayer();
        $user = Main::getInstance()->getUserManager()->getUser($player->getName());

        $item = $e->getItem();
        $namedTag = $item->getNamedTag();

        if($namedTag->getInt("safeId", -1) === -1) {
            return;
        }

        $safe = Main::getInstance()->getSafeManager()->getSafeById($namedTag->getInt("safeId"));

        if(!$safe)
            return;

        if($user->hasAntyLogout()) {
            $player->sendMessage(MessageUtil::format("Nie mozesz otworzyc sejfa podczas antylogouta!"));
            $e->cancel();
            return;
        }

        if($safe->getName() !== $player->getName()) {

            if($player->getInventory()->contains(($crowbar = (new Crowbar())->__toItem()))) {
                $player->getInventory()->removeItem($crowbar);
                $player->sendMessage(MessageUtil::format("Wlamano sie do sejfu!"));
                $safe->setName($player->getName());
                $player->getInventory()->setItemInHand((new Safe($safe))->__toItem());
            } else {
                $player->sendMessage(MessageUtil::format("Nie jestes wlascicielem tego sejfu!"));
                return;
            }
        }

        if($user->hasLastData(Settings::$SAFE_LAST_OPEN)) {
            if(!$user->hasLastData(Settings::$OPEN_SAFE_DELAY)) {
                $player->sendMessage(MessageUtil::format("Ponownie otworzyc sejf bedziesz mogl dopiero za §e" . ($user->getLastData(Settings::$SAFE_LAST_OPEN)["value"] - time()) . " §7sekund!"));
                $user->setLastData(Settings::$OPEN_SAFE_DELAY, (time() + Settings::$OPEN_SAFE_DELAY_TIME), Settings::$TIME_TYPE);
            }
            return;
        }

        SoundUtil::addSound([$player], $player->getPosition(), "random.shulkerboxopen");
        (new SafeInventory($safe))->openFor([$player]);
    }
}