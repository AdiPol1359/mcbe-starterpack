<?php

namespace core\item\items;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\item\Food;

class GoldenApple extends Food{

    public function __construct(int $meta = 0){
        parent::__construct(self::GOLDEN_APPLE, $meta, "Golden Apple");
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
            new EffectInstance(Effect::getEffect(Effect::REGENERATION), 50, 4),
            new EffectInstance(Effect::getEffect(Effect::ABSORPTION), 20*60*2, 0)
        ];
    }
}