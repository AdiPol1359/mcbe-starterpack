<?php

namespace core\generator\generators;

use pocketmine\block\Block;
use pocketmine\level\generator\Generator;
use pocketmine\math\Vector3;

class VoidGenerator extends Generator {

    private array $settings;

    public function __construct(array $settings = []) {
        $this->settings = $settings;
    }

    public function getName() : string {
        return "void";
    }

    public function getSettings() : array {
        return $this->settings;
    }

    public function getSpawn() : Vector3 {
        return new Vector3(1, 1, 1);
    }

    public function populateChunk(int $chunkX, int $chunkZ) : void {
    }

    public function generateChunk(int $chunkX, int $chunkZ) : void {
        $chunk = $this->level->getChunk($chunkX, $chunkZ);
        $chunk->setGenerated();

        if($chunkX == 0 && $chunkZ == 0){

            $chunk->setBlock(1, 0, 1, Block::INVISIBLE_BEDROCK);

            $chunk->setX($chunkX);
            $chunk->setZ($chunkZ);
            $this->level->setChunk($chunkX, $chunkZ, $chunk);
        }
    }
}