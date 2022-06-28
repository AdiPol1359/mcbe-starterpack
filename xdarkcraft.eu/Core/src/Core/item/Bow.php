<?php

declare(strict_types=1);

namespace Core\item;

use Core\Main;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Arrow as ArrowEntity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Player;
use pocketmine\item\{
	Item, ItemFactory, Tool
};

class Bow extends Tool {

    public const PUNCH_COOLDOWN = 5;

    private $lastPunch = [];

	public function __construct(int $meta = 0){
		parent::__construct(self::BOW, $meta, "Bow");
	}

	public function getFuelTime() : int{
		return 200;
	}

	public function getMaxDurability() : int{
		return 385;
	}
	
	private function punch(Player $player, ArrowEntity $arrow, float $punchKnockback) : void {
	    $nick = $player->getName();

        $lastPunch = (isset($this->lastPunch[$nick]) ? time() - $this->lastPunch[$nick] : time());

        if(isset(Main::$antylogoutPlayers[$nick]) && $lastPunch < self::PUNCH_COOLDOWN) {
            $player->sendMessage("§8§l>§r §7Puncha mozesz ponownie uzyc za §4".(self::PUNCH_COOLDOWN - $lastPunch)." §7sekund");
            return;
        }

		if($punchKnockback > 0){
			$punchKnockback += 0.5;
			if($player->isOnGround()){
			
	 		$motion = $arrow->getMotion();
			
	 		$horizontalSpeed = sqrt($motion->x ** 2 + $motion->z ** 2);
			
	 		if($horizontalSpeed > 0){
		 		$multiplier = $punchKnockback * 0.8 / $horizontalSpeed;
				
			 	$player->setMotion($player->getMotion()->add($motion->x * $multiplier, 0.5, $motion->z * $multiplier));
			 	
			 	$player->getLevel()->broadcastLevelSoundEvent($player, LevelSoundEventPacket::SOUND_BOW);
			 	$player->getLevel()->broadcastLevelSoundEvent($player, LevelSoundEventPacket::SOUND_BOW_HIT);
				
		 		$ev = new EntityDamageEvent($player, EntityDamageEvent::CAUSE_ENTITY_ATTACK, 0);
				
			 	$player->attack($ev);

			 	$this->lastPunch[$nick] = time();
				}
			}
		}
	}

	public function onReleaseUsing(Player $player) : bool{
		if($player->isSurvival() and !$player->getInventory()->contains(ItemFactory::get(Item::ARROW, 0, 1))){
			$player->getInventory()->sendContents($player);
			return false;
		}

		$nbt = Entity::createBaseNBT(
			$player->add(0, $player->getEyeHeight(), 0),
			$player->getDirectionVector(),
			($player->yaw > 180 ? 360 : 0) - $player->yaw,
			-$player->pitch
		);
		$nbt->setShort("Fire", $player->isOnFire() ? 45 * 60 : 0);

		$diff = $player->getItemUseDuration();
		$p = $diff / 20;
		$baseForce = min((($p ** 2) + $p * 2) / 3, 1);
		
		$entity = Entity::createEntity("Arrow", $player->getLevel(), $nbt, $player, $baseForce >= 1);
		if($entity instanceof Projectile){
			$infinity = $this->hasEnchantment(Enchantment::INFINITY);
			if($entity instanceof ArrowEntity){
				if($infinity){
					$entity->setPickupMode(ArrowEntity::PICKUP_CREATIVE);
				}
				if(($punchLevel = $this->getEnchantmentLevel(Enchantment::PUNCH)) > 0){
					$entity->setPunchKnockback($punchLevel);
				}
			}
			if(($powerLevel = $this->getEnchantmentLevel(Enchantment::POWER)) > 0){
				$entity->setBaseDamage($entity->getBaseDamage() + (($powerLevel + 1) / 2));
			}
			if($this->hasEnchantment(Enchantment::FLAME)){
				$entity->setOnFire(intdiv($entity->getFireTicks(), 20) + 100);
			}
			$ev = new EntityShootBowEvent($player, $this, $entity, $baseForce * 3);

			if($baseForce < 0.1 or $diff < 5){
				$ev->setCancelled();
			}
			
			if($diff == 3 || $diff == 4 || $diff == 5) {
				if(($lvl = $this->getEnchantmentLevel(Enchantment::PUNCH)) > 0) {
                    $this->punch($player, $entity, $lvl);
                }
			}

			$ev->call();

			$entity = $ev->getProjectile(); //This might have been changed by plugins

			if($ev->isCancelled()){
				$entity->flagForDespawn();
				$player->getInventory()->sendContents($player);
			}else{
				$entity->setMotion($entity->getMotion()->multiply($ev->getForce()));
				if($player->isSurvival()){
					/*if(!$infinity){
						$player->getInventory()->removeItem(ItemFactory::get(Item::ARROW, 0, 1));
					}*/
					$this->applyDamage(1);
				}

				if($entity instanceof Projectile){
					$projectileEv = new ProjectileLaunchEvent($entity);
					$projectileEv->call();
					if($projectileEv->isCancelled()){
						$ev->getProjectile()->flagForDespawn();
					}else{
						$ev->getProjectile()->spawnToAll();
						$player->getLevel()->broadcastLevelSoundEvent($player, LevelSoundEventPacket::SOUND_BOW);
					}
				}else{
					$entity->spawnToAll();
				}
			}
		}else{
			$entity->spawnToAll();
		}

		return true;
	}
}