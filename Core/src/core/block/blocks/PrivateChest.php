<?php

namespace core\block\blocks;

use core\manager\managers\privatechest\ChestManager;
use core\util\utils\MessageUtil;
use pocketmine\block\Block;
use pocketmine\block\Chest as PMChest;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Chest as TileChest;
use pocketmine\tile\Tile;

class PrivateChest extends PMChest{

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool{

        $faces = [
            0 => 4,
            1 => 2,
            2 => 5,
            3 => 3
        ];

        $chest = null;
        $this->meta = $faces[$player instanceof Player ? $player->getDirection() : 0];

        for($side = 2; $side <= 5; ++$side){

            if(($this->meta === 4 or $this->meta === 5) and ($side === 4 or $side === 5))
                continue;

            elseif(($this->meta === 3 or $this->meta === 2) and ($side === 2 or $side === 3))
                continue;

            $c = $this->getSide($side);

            if($c->getId() === $this->id && $c->getDamage() === $this->meta){
                $tile = $this->getLevelNonNull()->getTile($c);

                if($tile instanceof TileChest && !$tile->isPaired()){
                    $chest = $tile;
                    break;
                }
            }
        }

        $this->getLevelNonNull()->setBlock($blockReplace, $this, true, true);
        $tile = Tile::createTile(Tile::CHEST, $this->getLevelNonNull(), TileChest::createNBT($this, $face, $item, $player));

        if($chest instanceof TileChest && $tile instanceof TileChest){

            if(ChestManager::isLocked($chest->asPosition())){
                if($player !== null)
                    $player->sendMessage(MessageUtil::format("Skrzynka obok jest zablokowana!"));

                return true;
            }

            $chest->pairWith($tile);
            $tile->pairWith($chest);
        }

        return true;
    }
}