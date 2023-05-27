<?php

declare(strict_types=1);

namespace core\entities\animation;

use core\entities\object\FireworksRocket;
use pocketmine\entity\animation\Animation;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\types\ActorEvent;

final class FireworkExplodeAnimation implements Animation {

    private FireworksRocket $firework;

    public function __construct(FireworksRocket $firework) {
        $this->firework = $firework;
    }

    public function encode() : array {
        return [ActorEventPacket::create($this->firework->getId(), ActorEvent::FIREWORK_PARTICLES, 0)];
    }
}