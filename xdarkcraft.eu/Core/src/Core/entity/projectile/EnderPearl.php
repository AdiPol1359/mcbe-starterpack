<?php

declare(strict_types=1);

namespace Core\entity\projectile;

use Core\api\ProtectAPI;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\entity\projectile\EnderPearl as PMEnderPearl;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\level\sound\EndermanTeleportSound;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use Core\Main;

class EnderPearl extends PMEnderPearl {

    protected function onHit(ProjectileHitEvent $event) : void{
        $owner = $this->getOwningEntity();

        if($owner !== null){
            $vector = $event->getRayTraceResult()->getHitVector();
            $x = $vector->getFloorX();
            $z = $vector->getFloorZ();

            $border = floor(Main::BORDER / 2);

            if($x >= $border || $x <= -$border || $z >= $border || $z <= -$border) {
                if($owner instanceof Player)
                    $owner->sendMessage("§8§l>§r §7Nie mozesz rzucic perly za border!");

                $owner->getInventory()->addItem(Item::get(Item::ENDER_PEARL));
                return;
            }

            $terrainName = ProtectAPI::getTerrainNameFromPos($vector);

            if($terrainName == "spawn" || $terrainName == "spawn-fly") {
                if($owner instanceof Player)
                    $owner->sendMessage("§8§l>§r §7Nie mozesz rzucic perly na spawn");

                $owner->getInventory()->addItem(Item::get(Item::ENDER_PEARL));
                return;
            }

            $this->level->broadcastLevelEvent($owner, LevelEventPacket::EVENT_PARTICLE_ENDERMAN_TELEPORT);
            $this->level->addSound(new EndermanTeleportSound($owner));
            $owner->teleport($vector);
            $this->level->addSound(new EndermanTeleportSound($owner));

            $owner->attack(new EntityDamageEvent($owner, EntityDamageEvent::CAUSE_FALL, 3));
        }
    }
}