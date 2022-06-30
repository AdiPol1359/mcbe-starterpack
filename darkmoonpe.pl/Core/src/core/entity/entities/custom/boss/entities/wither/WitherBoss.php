<?php

namespace core\entity\entities\custom\boss\entities\wither;

use core\entity\entities\custom\boss\Boss;
use core\entity\entities\custom\boss\BossIds;
use core\manager\managers\bossbar\BossbarManager;
use core\manager\managers\bossbar\bossbars\AreaBoss;
use core\manager\managers\ParticlesManager;
use core\manager\managers\quest\QuestManager;
use core\manager\managers\SoundManager;
use core\util\utils\ConfigUtil;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Projectile;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ProjectileLaunchEvent;
use pocketmine\level\Level;
use pocketmine\level\particle\HugeExplodeParticle;
use pocketmine\level\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\Player;
use pocketmine\Server;

class WitherBoss extends Boss {

    public const HEALTH = 50000;

    private int $shootTick;
    private int $witherSkeletonTick;

    public function __construct(Level $level, CompoundTag $nbt) {

        $this->width = 1.0;
        $this->height = 3.0;
        $this->bossId = 52;
        $this->bossDamage = 15;
        $this->attackDistance = 3.5;
        $this->shootTick = 0;
        $this->witherSkeletonTick = 0;

        parent::__construct($level, $nbt);

        if(($arena = Server::getInstance()->getLevelByName(ConfigUtil::BOSS_WORLD)) !== null)
            $this->teleport(new Position(0, 170, 0, $level));

        $this->setMaxHealth(self::HEALTH);
        $this->setHealth(self::HEALTH);
        $this->setMovementSpeed(0.07);

        $this->setScale(2.5);

        SoundManager::spawnSpecifySound($this->asPosition(), $this->getLevel()->getPlayers(), "minecraft:lightning_bolt");

        foreach($this->level->getPlayers() as $player)
            SoundManager::addSound($player, $this->asPosition(), "ambient.weather.lightning.impact", 1);

        foreach($this->level->getEntities() as $entity) {
            if($entity instanceof WitherBoss && $entity->getId() !== $this->getId())
                $entity->close();
        }
    }

    public function getType() : int {
        return BossIds::AREA_BOSS;
    }

    public function getName() : string {
        return "Wither Boss";
    }

    public function onUpdate(int $currentTick) : bool {

        if($this->closestPlayer) {
            if($this->witherSkeletonTick >= $this->calculateTimeSpawnWitherSkeleton()) {

                $this->witherSkeletonTick = 0;

                for($i = 1; $i <= 3; $i++) {
                    $nbt = Entity::createBaseNBT($this->asPosition()->add(mt_rand(1, 3), mt_rand(1, 3), mt_rand(1, 3)), null, $this->yaw, $this->pitch);

                    (new WitherSkeleton($this->level, $nbt))->spawnToAll();
                }
            }

            if($this->shootTick >= $this->calculateTimeShoot()) {
                $this->shootTick = 0;

                $nbt = Entity::createBaseNBT($this->add(0, $this->getEyeHeight() + 1), $this->closestPlayer, $this->yaw, $this->pitch);
                $projectile = Entity::createEntity("WitherSkull", $this->getLevelNonNull(), $nbt);

                if($projectile !== null)
                    $projectile->setMotion($projectile->getMotion()->multiply(1.5));

                if($projectile instanceof Projectile){
                    $projectileEv = new ProjectileLaunchEvent($projectile);
                    $projectileEv->call();
                    if($projectileEv->isCancelled()){
                        $projectile->flagForDespawn();
                    }else{
                        $projectile->spawnToAll();
                        $projectile->teleport($this->add(0, $this->getEyeHeight() + 5));

                        SoundManager::spawnSpecifySound($this, $this->level->getPlayers(), "mob.wither.shoot");
                    }
                }elseif($projectile !== null){
                    $projectile->spawnToAll();
                    $projectile->teleport($this->add(0, $this->getEyeHeight() + 5));
                }
            }

            $this->witherSkeletonTick++;
            $this->shootTick++;
        }

        return parent::onUpdate($currentTick);
    }

    public function attack(EntityDamageEvent $source) : void {

        if($source->getCause() === $source::CAUSE_FALL) {
            foreach($this->getLevel()->getPlayers() as $player) {
                ParticlesManager::spawnParticle($player, new HugeExplodeParticle($this->asPosition()));
                SoundManager::spawnSpecifySound($this->asPosition(), $this->getLevel()->getPlayers(), "minecraft:lightning_bolt");

                if($player->distance($this) <= 7)
                    $player->attack(new EntityDamageByEntityEvent($this, $player, EntityDamageEvent::CAUSE_ENTITY_ATTACK, 20));

                return;
            }
        }

        parent::attack($source);

        $this->updateBossBar();
    }

    public function getDrops() : array {
        return [];
    }

    public function getXpDropAmount() : int {
        return 0;
    }

    public function setHealth(float $amount) : void {
        parent::setHealth($amount);
        $this->updateBossBar();
    }

    public function despawnFrom(Player $player, bool $send = true) : void {
        parent::despawnFrom($player, $send);

        if(($bossbar = BossbarManager::getBossbar($player)) !== null) {

            if(!$bossbar instanceof AreaBoss)
                return;

            $bossbar->hideFrom($player);
            BossbarManager::unsetBossbar($player);
            QuestManager::send($player);
        }
    }

    private function calculateTimeShoot() : int {

        $time = 60;

        if($this->getHealth() <= (self::HEALTH / 2))
            $time = 40;

        if($this->getHealth() <= (self::HEALTH / 4))
            $time = 20;

        if($this->getHealth() <= (self::HEALTH / 10))
            $time = 10;

        return $time;
    }

    private function calculateTimeSpawnWitherSkeleton() : int {

        $time = 60*20;

        if($this->getHealth() <= (self::HEALTH / 2))
            $time = 40*20;

        if($this->getHealth() <= (self::HEALTH / 4))
            $time = 20*20;

        if($this->getHealth() <= (self::HEALTH / 10))
            $time = 10*20;

        return $time;
    }
}