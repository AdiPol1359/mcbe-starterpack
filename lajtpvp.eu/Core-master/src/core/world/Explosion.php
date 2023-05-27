<?php

declare(strict_types=1);

namespace core\world;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByBlockEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\item\ItemFactory;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\world\Explosion as PMExplosion;
use pocketmine\world\format\SubChunk;
use pocketmine\world\particle\HugeExplodeSeedParticle;
use pocketmine\world\Position;
use pocketmine\world\sound\ExplodeSound;
use pocketmine\world\utils\SubChunkExplorer;
use pocketmine\world\utils\SubChunkExplorerStatus;
use pocketmine\world\World;

class Explosion extends PMExplosion {

    /** @var int */
    private int $rays = 16;

    /** @var Entity|Block|null */
    private Entity|Block|null $what;

    /** @var SubChunkExplorer */
    private SubChunkExplorer $subChunkExplorer;

    public function __construct(Position $center, float $size, $what = null){
        if(!$center->isValid()){
            throw new \InvalidArgumentException("Position does not have a valid world");
        }
        $this->source = $center;
        $this->world = $center->getWorld();

        if($size <= 0){
            throw new \InvalidArgumentException("Explosion radius must be greater than 0, got $size");
        }
        $this->size = $size;

        $this->what = $what;
        $this->subChunkExplorer = new SubChunkExplorer($this->world);
    }

    public function explodeA() : bool{
        if($this->size < 0.1){
            return false;
        }

        $blockFactory = BlockFactory::getInstance();

        $currentChunk = null;
        $currentSubChunk = null;

        $mRays = $this->rays - 1;
        for($i = 0; $i < $this->rays; ++$i){
            for($j = 0; $j < $this->rays; ++$j){
                for($k = 0; $k < $this->rays; ++$k){
                    if($i === 0 or $i === $mRays or $j === 0 or $j === $mRays or $k === 0 or $k === $mRays){
                        //this could be written as new Vector3(...)->normalize()->multiply(stepLen), but we're avoiding Vector3 for performance here
                        [$shiftX, $shiftY, $shiftZ] = [$i / $mRays * 2 - 1, $j / $mRays * 2 - 1, $k / $mRays * 2 - 1];
                        $len = sqrt($shiftX ** 2 + $shiftY ** 2 + $shiftZ ** 2);
                        [$shiftX, $shiftY, $shiftZ] = [($shiftX / $len) * $this->stepLen, ($shiftY / $len) * $this->stepLen, ($shiftZ / $len) * $this->stepLen];
                        $pointerX = $this->source->x;
                        $pointerY = $this->source->y;
                        $pointerZ = $this->source->z;

                        for($blastForce = $this->size * (mt_rand(700, 1300) / 1000); $blastForce > 0; $blastForce -= $this->stepLen * 0.75){
                            $x = (int) $pointerX;
                            $y = (int) $pointerY;
                            $z = (int) $pointerZ;
                            $vBlockX = $pointerX >= $x ? $x : $x - 1;
                            $vBlockY = $pointerY >= $y ? $y : $y - 1;
                            $vBlockZ = $pointerZ >= $z ? $z : $z - 1;

                            $pointerX += $shiftX;
                            $pointerY += $shiftY;
                            $pointerZ += $shiftZ;

                            if($this->subChunkExplorer->moveTo($vBlockX, $vBlockY, $vBlockZ) === SubChunkExplorerStatus::INVALID){
                                continue;
                            }

                            $state = $this->subChunkExplorer->currentSubChunk->getFullBlock($vBlockX & SubChunk::COORD_MASK, $vBlockY & SubChunk::COORD_MASK, $vBlockZ & SubChunk::COORD_MASK);

                            $blastResistance = $blockFactory->blastResistance[$state];
                            if($blastResistance >= 0){
                                $blastForce -= ($blastResistance / 5 + 0.3) * $this->stepLen;
                                if($blastForce > 0){
                                    if(!isset($this->affectedBlocks[World::blockHash($vBlockX, $vBlockY, $vBlockZ)])){
                                        $_block = $this->world->getBlockAt($vBlockX, $vBlockY, $vBlockZ, true, false);
                                        foreach($_block->getAffectedBlocks() as $_affectedBlock){
                                            $_affectedBlockPos = $_affectedBlock->getPosition();
                                            $this->affectedBlocks[World::blockHash($_affectedBlockPos->x, $_affectedBlockPos->y, $_affectedBlockPos->z)] = $_affectedBlock;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return true;
    }

    public function explodeB() : bool{
        $updateBlocks = [];

        $source = (new Vector3($this->source->x, $this->source->y, $this->source->z))->floor();
        $yield = (1 / $this->size) * 100;

        if($this->what instanceof Entity){
            $ev = new EntityExplodeEvent($this->what, $this->source, $this->affectedBlocks, $yield);
            $ev->call();
            if($ev->isCancelled()){
                return false;
            }else{
                $yield = $ev->getYield();
                $this->affectedBlocks = $ev->getBlockList();
            }
        }

        $explosionSize = $this->size * 2;
        $minX = (int) floor($this->source->x - $explosionSize - 1);
        $maxX = (int) ceil($this->source->x + $explosionSize + 1);
        $minY = (int) floor($this->source->y - $explosionSize - 1);
        $maxY = (int) ceil($this->source->y + $explosionSize + 1);
        $minZ = (int) floor($this->source->z - $explosionSize - 1);
        $maxZ = (int) ceil($this->source->z + $explosionSize + 1);

        $explosionBB = new AxisAlignedBB($minX, $minY, $minZ, $maxX, $maxY, $maxZ);

        $list = $this->world->getNearbyEntities($explosionBB, $this->what instanceof Entity ? $this->what : null);
        foreach($list as $entity){
            $entityPos = $entity->getPosition();
            $distance = $entityPos->distance($this->source) / $explosionSize;

            if($distance <= 1){
                $motion = $entityPos->subtractVector($this->source)->normalize();

                $impact = (1 - $distance) * ($exposure = 1);

                $damage = (int) ((($impact * $impact + $impact) / 2) * 8 * $explosionSize + 1);

                if($this->what instanceof Entity){
                    $ev = new EntityDamageByEntityEvent($this->what, $entity, EntityDamageEvent::CAUSE_ENTITY_EXPLOSION, $damage);
                }elseif($this->what instanceof Block){
                    $ev = new EntityDamageByBlockEvent($this->what, $entity, EntityDamageEvent::CAUSE_BLOCK_EXPLOSION, $damage);
                }else{
                    $ev = new EntityDamageEvent($entity, EntityDamageEvent::CAUSE_BLOCK_EXPLOSION, $damage);
                }

                $entity->attack($ev);
                $entity->setMotion($motion->multiply($impact));
            }
        }

        $air = ItemFactory::air();
        $airBlock = VanillaBlocks::AIR();

        foreach($this->affectedBlocks as $block){
            $pos = $block->getPosition();
            if(mt_rand(0, 100) < $yield){
                foreach($block->getDrops($air) as $drop){
                    $this->world->dropItem($pos->add(0.5, 0.5, 0.5), $drop);
                }
            }
            if(($t = $this->world->getTileAt($pos->x, $pos->y, $pos->z)) !== null){
                $t->onBlockDestroyed(); //needed to create drops for inventories
            }
            $this->world->setBlockAt($pos->x, $pos->y, $pos->z, $airBlock);
        }

        $this->world->addParticle($source, new HugeExplodeSeedParticle());
        $this->world->addSound($source, new ExplodeSound());

        return true;
    }
}