<?php

declare(strict_types=1);

namespace core\items;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\GoldenAppleEnchanted;

class EnchantedGoldenApple extends GoldenAppleEnchanted {
    public function getAdditionalEffects() : array{
        return [
            new EffectInstance(VanillaEffects::REGENERATION(), 20*20, 4),
            new EffectInstance(VanillaEffects::RESISTANCE(), 20*60*5, 0),
            new EffectInstance(VanillaEffects::FIRE_RESISTANCE(), 20*60*5, 0),
            new EffectInstance(VanillaEffects::ABSORPTION(), 20*60, 2)
        ];
    }
}