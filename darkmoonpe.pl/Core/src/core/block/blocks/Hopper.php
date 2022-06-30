<?php

namespace core\block\blocks;

use core\tile\tiles\Hopper as HopperTile;
use pocketmine\block\Block;
use pocketmine\block\Hopper as PMHopper;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Tile;

class Hopper extends PMHopper {
    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool {

        static $faces = [
            0 => Vector3::SIDE_DOWN,
            1 => Vector3::SIDE_DOWN,
            2 => Vector3::SIDE_SOUTH,
            3 => Vector3::SIDE_NORTH,
            4 => Vector3::SIDE_EAST,
            5 => Vector3::SIDE_WEST
        ];

        $this->meta = $faces[$face];
        $this->getLevel()->setBlock($this, $this, true, true);

        Tile::createTile(Tile::HOPPER, $this->getLevel(), HopperTile::createNBT($this, $face, $item, $player));

        return true;
    }
}