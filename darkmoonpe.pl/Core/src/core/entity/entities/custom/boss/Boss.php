<?php

namespace core\entity\entities\custom\boss;

use core\manager\managers\bossbar\BossbarManager;
use core\manager\managers\bossbar\bossbars\AreaBoss;
use core\manager\managers\ParticlesManager;
use core\manager\managers\quest\QuestManager;
use core\manager\managers\SoundManager;
use pocketmine\block\Block;
use pocketmine\block\Flowable;
use pocketmine\entity\Creature;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Level;
use pocketmine\level\particle\HugeExplodeParticle;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\Player;

abstract class Boss extends Creature implements BossIds {

    protected int $bossId;

    protected float $bossDamage = 3;
    protected float $attackDistance = 4.3;

    protected ?Player $closestPlayer = null;

    protected int $healthTick = 0;
    protected $jumpVelocity = 0.84;

    public function __construct(Level $level, CompoundTag $nbt) {
        parent::__construct($level, $nbt);
    }

    public function onUpdate(int $currentTick) : bool {

        $this->updateClosestPlayer();

        if($this->deadTicks <= 0) {
            $this->healthTick++;

            if($this->healthTick >= 40) {
                if($this->getHealth() + 2 <= $this->getMaxHealth())
                    $this->setHealth(($this->getHealth() + 2));
                else
                    $this->setHealth($this->getMaxHealth());

                $this->healthTick = 0;
            }
        }

        $block = null;

        for($dist = 1; $dist <= $this->getScale(); $dist++) {
            if($block !== null) {
                if(!$block instanceof Flowable && $block->getId() !== Block::AIR && $this->isOnGround())
                    break;
            }

            switch($this->getDirection()) {
                case 0:
                    $blockPos = $this->add($dist);
                    break;
                case 1:
                    $blockPos = $this->add(0, 0, $dist);
                    break;
                case 2:
                    $blockPos = $this->add(-$dist);
                    break;
                case 3:
                    $blockPos = $this->add(0, 0, -$dist);
                    break;
                default:
                    return false;
            }

            $blockSave = $this->level->getBlock($blockPos);
            if(!$block instanceof Flowable && $blockSave->getId() !== Block::AIR && $this->isOnGround())
                $block = $blockSave;
        }

        if($block)
            $this->jump();

        $this->follow();

        return parent::onUpdate($currentTick);
    }

    abstract public function getType() : int;

    protected function sendSpawnPacket(Player $player): void {
        $pk = new AddActorPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->type = AddActorPacket::LEGACY_ID_MAP_BC[$this->bossId];
        $pk->position = $this->asVector3();
        $pk->motion = $this->getMotion();
        $pk->yaw = $this->yaw;
        $pk->headYaw = $this->yaw;
        $pk->pitch = $this->pitch;
        $pk->attributes = $this->attributeMap->getAll();
        $pk->metadata = $this->propertyManager->getAll();

        $player->dataPacket($pk);
    }

    public function spawnTo(Player $player) : void {

        $boss = BossbarManager::getBossbar($player);

        if($boss) {
            $boss->hideFrom($player);
            BossbarManager::unsetBossbar($player);
        }

        $bossbar = new AreaBoss("");
        $percentage = ($this->getHealth() / $this->getMaxHealth()) * 100;
        $bossbar->setHealthPercent($percentage / 100);
        $bossbar->setSubTitle("§9§l".$this->getName()." §r§8(§9".number_format($this->getHealth(), 2, ".", "")."§7/§9".number_format($this->getMaxHealth(), 2, ".", "")."§8)");
        $bossbar->showTo($player);

        BossbarManager::setBossbar($player, $bossbar);

        parent::spawnTo($player);
    }

    public function onDeath() : void {
        parent::onDeath();

        foreach($this->level->getPlayers() as $player) {
            ParticlesManager::spawnParticle($player, new HugeExplodeParticle($this->asPosition()));
            SoundManager::addSound($player, $this->asPosition(), "ambient.weather.lightning.impact", 1);
            ParticlesManager::spawnFireworkAt($player, $player->getLevel(), [[ParticlesManager::TYPE_HUGE_SPHERE, ParticlesManager::COLOR_DARK_GRAY], [ParticlesManager::TYPE_SMALL_SPHERE, ParticlesManager::COLOR_GRAY]], $this->asPosition());

            if(($bossbar = BossbarManager::getBossbar($player)) !== null) {

                if(!$bossbar instanceof AreaBoss)
                    continue;

                $bossbar->hideFrom($player);
                BossbarManager::unsetBossbar($player);
                QuestManager::send($player);
            }
        }

        SoundManager::spawnSpecifySound($this->asPosition(), $this->getLevel()->getPlayers(), "minecraft:lightning_bolt");
    }

    protected function updateBossBar() : void {
        foreach($this->level->getPlayers() as $player) {
            $bossbar = BossbarManager::getBossbar($player);

            if($bossbar instanceof AreaBoss) {
                $percentage = ($this->getHealth() / $this->getMaxHealth()) * 100;
                $bossbar->setHealthPercent($percentage / 100);
                $bossbar->setSubTitle("§9§l" . $this->getName() . " §r§8(§9" . number_format($this->getHealth(), 2, ".", "") . "§7/§9" . number_format($this->getMaxHealth(), 2, ".", "") . "§8)");
            }
        }
    }

    private function updateClosestPlayer() : void {
        $closestPlayer = null;

        foreach($this->level->getPlayers() as $player) {

            if($player->isCreative())
                continue;

            if(!$closestPlayer)
                $closestPlayer = $player;

            if($closestPlayer->distance($this) > $player->distance($this))
                $closestPlayer = $player;
        }

        $this->closestPlayer = $closestPlayer;
    }

    private function follow() : void {

        if(!$this->closestPlayer)
            return;

        $this->setHeadOnOwner();

        if($this->distance($this->closestPlayer) < $this->attackDistance) {
            $this->closestPlayer->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), 20*10));
            $this->closestPlayer->attack(new EntityDamageByEntityEvent($this, $this->closestPlayer, EntityDamageEvent::CAUSE_ENTITY_ATTACK, $this->bossDamage));
            return;
        }

        $x = floor($this->closestPlayer->x - $this->x);
        $z = floor($this->closestPlayer->z - $this->z);

        $xz = sqrt($x * $x + $z * $z);

        if($xz == 0)
            return;

        $speed = $this->getMovementSpeed();
        $this->motion->x = $speed * ($x / $xz);
        $this->motion->z = $speed * ($z / $xz);

        $this->move($this->motion->x, $this->motion->y, $this->motion->z);
    }

    private function setHeadOnOwner() : void {

        if(!$this->closestPlayer)
            return;

        $x = $this->closestPlayer->getX() - $this->getX();
        $y = $this->closestPlayer->getY() - $this->getY();
        $z = $this->closestPlayer->getZ() - $this->getZ();

        $len = sqrt($x * $x + $y + $z * $z);

        if($len == 0)
            return;

        $y = $y / $len;

        $pitch = -(asin($y) * 180 / M_PI);

        $yaw = -atan2($x, $z) * (180 / M_PI);

        if(!($pitch < 89) && !($pitch > -89))
            return;

        $this->yaw = $yaw;
        $this->pitch = $pitch;
    }
}