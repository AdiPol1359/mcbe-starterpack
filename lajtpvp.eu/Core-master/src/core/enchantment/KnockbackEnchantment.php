<?php

declare(strict_types=1);

namespace core\enchantment;

use pocketmine\entity\{
	Entity, Living
};

use pocketmine\item\enchantment\KnockbackEnchantment as PMKnockbackEnchantment;

class KnockbackEnchantment extends PMKnockbackEnchantment {

    public function onPostAttack(Entity $attacker, Entity $victim, int $enchantmentLevel) : void {
        if($victim instanceof Living) {

            $victimPosition = $victim->getPosition();
            $attackerPosition = $attacker->getPosition();

            $base = 0.17 + (0.11 * $enchantmentLevel);

            $deltaX = $victimPosition->x - $attackerPosition->x;
            $deltaZ = $victimPosition->z - $attackerPosition->z;

            $yaw = $attacker->getLocation()->getYaw();

            $rotation = fmod($yaw - 90, 360);
            if($rotation < 0) {
                $rotation += 360.0;
            }

            if($deltaX == 0 || $deltaZ == 0) {
                if((0 <= $rotation && $rotation < 45) || (315 <= $rotation && $rotation < 360)) {
                    $deltaX = ($victimPosition->x + (-1)) - $attackerPosition->x; //North
                } elseif(45 <= $rotation && $rotation < 135) {
                    $deltaZ = ($victimPosition->z + (-1)) - $attackerPosition->z; //East
                } elseif(135 <= $rotation && $rotation < 225) {
                    $deltaX = ($victimPosition->x + 1) - $attackerPosition->x; //South
                } elseif(225 <= $rotation && $rotation < 315) {
                    $deltaZ = ($victimPosition->z + 1) - $attackerPosition->z; //West
                }
            }

            $this->knockBack($victim, $deltaX, $deltaZ, $base);
        }
    }

    /*
     *
     * 	public function getDirection() : ?int{
		$rotation = fmod($this->yaw - 90, 360);
		if($rotation < 0){
			$rotation += 360.0;
		}
		if((0 <= $rotation and $rotation < 45) or (315 <= $rotation and $rotation < 360)){
			return 2; //North
		}elseif(45 <= $rotation and $rotation < 135){
			return 3; //East
		}elseif(135 <= $rotation and $rotation < 225){
			return 0; //South
		}elseif(225 <= $rotation and $rotation < 315){
			return 1; //West
		}else{
			return null;
		}
	}

     */
    private function knockBack(Entity $victim, float $x, float $z, float $base = 0.3) {

        $m = 3;

        $f = sqrt($x * $x + $z * $z);

        if($f <= 0)
            return;

        $f = 1 / $f;

        $motion = clone $victim->getMotion();

        $motion->x /= 2;
        $motion->y /= 2;
        $motion->z /= 2;

        if($x >= 2 || $z >= 2)
            $m = 4;
        $motion->x += ($x * $f * $base) * $m;
        $motion->z += ($z * $f * $base) * $m;

        $victim->setMotion($motion);
    }
}