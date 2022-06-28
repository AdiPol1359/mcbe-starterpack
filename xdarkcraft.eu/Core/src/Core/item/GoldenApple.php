<?php

declare(strict_types=1);

namespace Core\item;

use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;

use pocketmine\item\GoldenApple as PMGoldenApple;

class GoldenApple extends PMGoldenApple {
	public function getAdditionalEffects() : array{
		return [
			new EffectInstance(Effect::getEffect(Effect::REGENERATION), 20 * 3, 3),
			new EffectInstance(Effect::getEffect(Effect::FIRE_RESISTANCE), 20 * 10),
			new EffectInstance(Effect::getEffect(Effect::ABSORPTION), 20 * 120)
		];
	}
}