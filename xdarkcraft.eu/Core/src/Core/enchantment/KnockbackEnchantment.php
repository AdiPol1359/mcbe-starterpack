<?php

namespace Core\enchantment;

use pocketmine\entity\{
	Entity, Living
};

use pocketmine\item\enchantment\KnockbackEnchantment as PMKnockbackEnchantment;

use pocketmine\math\Vector3;

class KnockbackEnchantment extends PMKnockbackEnchantment {
	
	public function onPostAttack(Entity $attacker, Entity $victim, int $eLvL) : void {
		if($victim instanceof Living){
			
			$base = 0.2 + (0.085 * $eLvL);
			
			$deltaX = $victim->x - $attacker->x;
			$deltaZ = $victim->z - $attacker->z;
			
			if($deltaX == 0 || $deltaZ == 0){
				switch($attacker->getDirection()){
					case 0:				
			 		$deltaX = ($victim->x + 1) - $attacker->x;				
					break;
					
					case 1:
			 		$deltaZ = ($victim->z + 1) - $attacker->z;	
					break;
					
					case 2:
		 			$deltaX = ($victim->x + (-1)) - $attacker->x;
					break;
					
					case 3:
				 	$deltaZ = ($victim->z + (-1)) - $attacker->z;
					break;
				}
			}
			
			$this->knockBack($victim, $deltaX, $deltaZ, $base);
		}
	}
	
	private function knockBack(Entity $victim, float $x, float $z, float $base = 0.4) {
		$m = 3;
		
		$f = sqrt($x * $x + $z * $z);
		
		if($f <= 0)
		return;
		
		$f = 1 / $f;

		$motion = clone $victim->getMotion();
			
	 $motion->x /= 2;
		$motion->y /= 2;
		$motion->z /= 2;
			
		if($x >= 2 || $z >= 2) $m = 4;
			$motion->x += ($x * $f * $base) * $m;
			$motion->y += $base;
			$motion->z += ($z * $f * $base) * $m;
			
			if(!($victim->onGround)){
				$motion->y -= $base;
				$motion->y += ($base + ($base / 2));
			}

			$victim->setMotion($motion);
 }
}