<?php

namespace core\generator\generators;

use pocketmine\level\generator\Generator;

use pocketmine\math\Vector3;

use pocketmine\block\Block;

class CaveGenerator extends Generator {

    private array $populators = [];
    private array $generationPopulators = [];
    private array $settings;

    public function __construct(array $settings = []) {
        $this->settings = $settings;
    }

    public function getName() : string {
        return "CaveGenerator";
    }

    public function getSettings() : array {
        return $this->settings;
    }

    public function getSpawn() : Vector3 {
        return new Vector3(0, 50, 0);
    }

    public function generateChunk(int $chunkX, int $chunkZ) : void {
        $chunk = $this->level->getChunk($chunkX, $chunkZ);

        for($x = 0; $x < 16; ++$x) {
            for($z = 0; $z < 16; ++$z) {
                for($y = 0; $y < 100; ++$y) {
                    $chunk->setBlockId($x, $y, $z, Block::BEDROCK);
                    continue;
                }
                for($y = 0; $y < 0; ++$y) {
                    $chunk->setBlockId($x, $y, $z, Block::BEDROCK);
                    continue;
                }

                for($y = 1; $y < 99; $y++) {
                    $chunk->setBlockId($x, $y, $z, Block::STONE);
                    continue;
                }

                foreach($this->generationPopulators as $populator)
                    $populator->populate($this->level, $chunkX, $chunkZ, $this->random);
            }
        }
    }

    public function populateChunk(int $chunkX, int $chunkZ) : void {
        foreach($this->populators as $populator) {
            $populator->populate($this->level, $chunkX, $chunkZ);
        }
    }
}