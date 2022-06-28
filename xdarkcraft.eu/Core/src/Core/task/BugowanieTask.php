<?php

namespace Core\task;

use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\block\{
    Block, BlockFactory
};
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;

class BugowanieTask extends Task {

    private $player;
    private $block;
    private $time = 20*3; //3 SECONDS

    public function __construct(Player $player, Block $block) {
        $this->player = $player;
        $this->block = $block;
    }

    public function onRun(int $currentTick) : void {
        if(!$this->player->isOnline()) {
            $this->getHandler()->cancel();
            return;
        }

        $player = $this->player;
        $block = $this->block;

        if($this->time <= 0) {
            $pk = new UpdateBlockPacket();
            $pk->x = $block->x;
            $pk->y = $block->y;
            $pk->z = $block->z;
            $pk->flags = UpdateBlockPacket::FLAG_ALL;
            $pk->blockRuntimeId = BlockFactory::toStaticRuntimeId($player->getLevel()->getBlock($block)->getId(), $player->getLevel()->getBlock($block)->getId());

            $player->dataPacket($pk);
            $this->getHandler()->cancel();
            return;
        }

        $pk = new UpdateBlockPacket();
        $pk->x = $block->x;
        $pk->y = $block->y;
        $pk->z = $block->z;
        $pk->flags = UpdateBlockPacket::FLAG_ALL;
        $pk->blockRuntimeId = BlockFactory::toStaticRuntimeId($block->getId(), $block->getDamage());

        $player->dataPacket($pk);

        $this->time--;
    }

}