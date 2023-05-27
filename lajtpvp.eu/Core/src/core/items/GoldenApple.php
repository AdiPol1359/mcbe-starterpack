<?php

declare(strict_types=1);

namespace core\items;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\GoldenApple as PMGoldenApple;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;

class GoldenApple extends PMGoldenApple{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIds::GOLDEN_APPLE, 0), "Golden Apple");
    }

    public function requiresHunger() : bool{
        return false;
    }

    public function getFoodRestore() : int{
        return 4;
    }

    public function getSaturationRestore() : float{
        return 9.6;
    }

    public function getAdditionalEffects() : array{
        return [
            new EffectInstance(VanillaEffects::REGENERATION(), 20*6, 3),
            new EffectInstance(VanillaEffects::ABSORPTION(), 20*60, 2),
            new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 20*5, 0)
        ];
    }
}