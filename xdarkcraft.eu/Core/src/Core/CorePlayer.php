<?php

declare(strict_types=1);

namespace Core;

use pocketmine\event\entity\{
    EntityDamageEvent, EntityDamageByEntityEvent
};
use pocketmine\Player;
use pocketmine\entity\{Effect, EffectInstancef, Living};
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\item\{
    Totem, Durable
};

class CorePlayer extends Player {
	
	public function getWindows() {
		return $this->windows;
	}

    private function damageItem(Durable $item, int $durabilityRemoved) : void{
        $item->applyDamage($durabilityRemoved);
        if($item->isBroken()){
            $this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_BREAK);
        }
    }

    protected function applyPostDamageEffects(EntityDamageEvent $source) : void{
        $totemModifier = $source->getModifier(EntityDamageEvent::MODIFIER_TOTEM);
        if($totemModifier < 0){
            $this->removeAllEffects();

            $this->addEffect(new EffectInstance(Effect::getEffect(Effect::REGENERATION), 40 * 20, 1));
            $this->addEffect(new EffectInstance(Effect::getEffect(Effect::FIRE_RESISTANCE), 40 * 20, 1));
            $this->addEffect(new EffectInstance(Effect::getEffect(Effect::ABSORPTION), 5 * 20, 1));

            $this->broadcastEntityEvent(ActorEventPacket::CONSUME_TOTEM);
            $this->level->broadcastLevelEvent($this->add(0, $this->eyeHeight, 0), LevelEventPacket::EVENT_SOUND_TOTEM);

            $hand = $this->inventory->getItemInHand();
            if($hand instanceof Totem){
                $hand->pop(); //Plugins could alter max stack size
                $this->inventory->setItemInHand($hand);
            }
        }

        $this->setAbsorption(max(0, $this->getAbsorption() + $source->getModifier(EntityDamageEvent::MODIFIER_ABSORPTION)));

        $armorDamage = $source->getBaseDamage();
        if($source instanceof EntityDamageByEntityEvent){
            $damage = 0;
            $damager = $source->getDamager();

            if(!$damager instanceof Living)
                return;
            
            if($damager->hasEffect(Effect::STRENGTH)) {
                $level = $damager->getEffect(Effect::STRENGTH)->getEffectLevel();
                $armorDamage *= ($level*1.95);
            }

            foreach($this->armorInventory->getContents() as $k => $item){
                if($item instanceof Armor and ($thornsLevel = $item->getEnchantmentLevel(Enchantment::THORNS)) > 0){
                    if(mt_rand(0, 99) < $thornsLevel * 15){
                        $this->damageItem($item, 3);
                        $damage += ($thornsLevel > 10 ? $thornsLevel - 10 : 1 + mt_rand(0, 3));
                    }else{
                        $this->damageItem($item, 1); //thorns causes an extra +1 durability loss even if it didn't activate
                    }

                    $this->armorInventory->setItem($k, $item);
                }
            }

            if($damage > 0){
                $source->getDamager()->attack(new EntityDamageByEntityEvent($this, $source->getDamager(), EntityDamageEvent::CAUSE_MAGIC, $damage));
            }
        }

        $this->damageArmor($armorDamage);
    }
}