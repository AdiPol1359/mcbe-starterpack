<?php

declare(strict_types=1);

namespace core\entities\object;

use core\Main;
use core\utils\MessageUtil;
use core\utils\Settings;
use pocketmine\entity\projectile\EnderPearl as PMEnderPearl;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\world\particle\EndermanTeleportParticle;
use pocketmine\world\Position;
use pocketmine\world\sound\EndermanTeleportSound;

class EnderPearl extends PMEnderPearl {

    protected function onHit(ProjectileHitEvent $event) : void{
        $owner = $this->getOwningEntity();

        if($owner !== null){
            $vector = $event->getRayTraceResult()->getHitVector();
            $x = $vector->getFloorX();
            $z = $vector->getFloorZ();

            $border = Settings::$BORDER_DATA["border"];

            if($owner instanceof Player) {

                $user = Main::getInstance()->getUserManager()->getUser($owner->getName());

                if(!$user)
                    return;

                $user->removeEnderPearl($this->id);

                if ($x >= $border || $x <= -$border || $z >= $border || $z <= -$border) {
                    $owner->sendMessage("§8» §7Nie mozesz rzucic perly za border!");
                    return;
                }

                $terrains = Main::getInstance()->getTerrainManager()->getTerrainsFromPos(Position::fromObject($event->getRayTraceResult()->getHitVector(), $this->getWorld()));

                if($user->hasAntyLogout()) {
                    foreach($terrains as $terrain) {
                        if($terrain->getName() === Settings::$SPAWN_TERRAIN) {
                            $owner->sendMessage(MessageUtil::format("Nie mozesz sie wytepac w bezpieczne miejsce podczas antylogouta!"));
                            $owner->getInventory()->addItem(VanillaItems::ENDER_PEARL());
                            return;
                        }
                    }
                }

                $user->addLastEnderPearl($vector->floor());
            }

            $this->getWorld()->addParticle($origin = $owner->getPosition(), new EndermanTeleportParticle());
            $this->getWorld()->addSound($origin, new EndermanTeleportSound());
            $owner->teleport($target = $event->getRayTraceResult()->getHitVector());
            $this->getWorld()->addSound($target, new EndermanTeleportSound());
        }
    }
}