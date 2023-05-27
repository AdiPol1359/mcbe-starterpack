<?php

namespace core\entities\object;

use core\world\Explosion;
use pocketmine\block\BlockLegacyIds;
use pocketmine\entity\Location;
use pocketmine\entity\object\PrimedTNT as PMPrimedTNT;
use pocketmine\event\entity\ExplosionPrimeEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\Position;
use pocketmine\world\sound\IgniteSound;

class PrimedTNT extends PMPrimedTNT {

    private CompoundTag $compound;
    private Vector3 $spawnPosition;

    public const DATA_FUSE_LENGTH = 55; //int

    public function __construct(Location $location, CompoundTag $nbt) {
        $this->spawnPosition = new Vector3($location->x, $location->y, $location->z);
        $this->gravity = 0.03;

        $this->compound = $nbt;

        parent::__construct($location, $nbt);

        $this->spawnToAll();
        $this->broadcastSound(new IgniteSound());
    }

    public function entityBaseTick(int $tickDiff = 1) : bool {
        if($this->compound->getTag("thrownTnt")) {
            if($this->closed){
                return false;
            }

            if($this->fuse % 5 === 0){
                $this->getNetworkProperties()->setInt(self::DATA_FUSE_LENGTH, $this->fuse);
            }

            if(!$this->isFlaggedForDespawn()){
                $this->fuse -= 1;

                if($this->fuse <= 0){
                    $this->flagForDespawn();
                    $this->explode();
                }
            }
        } else {
            if($this->getPosition()->distance($this->getSpawnPosition()) > 5) {
                $blocks = [];

                for($yy = -1; $yy <= 1; $yy++) {
                    for($xx = -1; $xx <= 1; $xx++) {
                        for($zz = -1; $zz <= 1; $zz++) {
                            $actualPosition = clone $this->getPosition()->floor();

                            $actualPosition = $actualPosition->sum($actualPosition, new Vector3($xx, $yy, $zz));
                            $blocks[] = $this->getWorld()->getBlock($actualPosition);
                        }
                    }
                }

                foreach($blocks as $block) {
                    if($block->getId() !== BlockLegacyIds::AIR) {
                        if(!$this->getWorld())
                            continue;

                        $this->explode();
                        $this->flagForDespawn();
                        break;
                    }
                }
            }
        }

        return parent::entityBaseTick($tickDiff);
    }

    public function getSpawnPosition() : Vector3 {
        return $this->spawnPosition;
    }

    public function setMotion(Vector3 $motion) : bool {
        if($this->isClosed() || $this->motion === null) {
            return false;
        }

        if($this->getMotion()->x != 0 && $this->getMotion()->z != 0) {
            if($this->getMotion()->x > 0) {
                if($motion->x < $this->getMotion()->x)
                    return true;
            } else {
                if($motion->x > $this->getMotion()->x)
                    return true;
            }

            if($this->getMotion()->z > 0) {
                if($motion->z < $this->getMotion()->z)
                    return true;
            } else {
                if($motion->z > $this->getMotion()->z)
                    return true;
            }

            $motion->y = $this->getMotion()->y;

        } elseif($this->getPosition()->distance($this->getSpawnPosition()) > 5)
            return true;

        return parent::setMotion($motion);
    }

    public function explode() : void{
        $ev = new ExplosionPrimeEvent($this, 4);
        $ev->call();
        if(!$ev->isCancelled()){
            $explosion = new Explosion(Position::fromObject($this->location->add(0, $this->size->getHeight() / 2, 0), $this->getWorld()), $ev->getForce(), $this);
            if($ev->isBlockBreaking()){
                $explosion->explodeA();
            }
            $explosion->explodeB();
        }
    }
}