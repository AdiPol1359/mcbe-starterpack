<?php

namespace core\entity\entities\projectile;

use core\Main;
use core\manager\managers\terrain\TerrainManager;
use core\util\utils\MessageUtil;
use pocketmine\entity\projectile\EnderPearl as PMEnderPearl;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\level\Position;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\Player;

class EnderPearl extends PMEnderPearl {

    public function onHit(ProjectileHitEvent $event) : void {
        $owner = $this->getOwningEntity();
        if($owner !== null){

            if($owner instanceof Player) {

                $terrains = TerrainManager::getTerrainsFromPos(Position::fromObject($event->getRayTraceResult()->getHitVector(), $this->level));

                if(isset(Main::$antylogout[$owner->getName()])) {
                    foreach($terrains as $terrain) {
                        if($terrain->getName() === "arenanopvp") {
                            $event->setCancelled(true);
                            $owner->sendMessage(MessageUtil::format("Nie mozesz sie wytepac w bezpieczne miejsce podczas antylogouta!"));
                            return;
                        }
                    }
                }

                foreach($terrains as $terrain) {
                    if($terrain->getName() === "bossnopvp") {
                        $event->setCancelled(true);
                        $owner->sendMessage(MessageUtil::format("Nie mozesz sie wytepac w bezpieczne miejsce na arenie bossow"));
                        return;
                    }
                }
            }
            $this->level->broadcastLevelEvent($owner, LevelEventPacket::EVENT_PARTICLE_ENDERMAN_TELEPORT);
            $this->level->addSound(new EndermanTeleportSound($owner));
            $owner->teleport($event->getRayTraceResult()->getHitVector());
            $this->level->addSound(new EndermanTeleportSound($owner));
        }
    }
}