<?php

declare(strict_types=1);

namespace Core\block;

use pocketmine\block\Transparent;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use Core\tile\{
	Tile, Beacon as TileBeacon
};

class Beacon extends Transparent {
	
	protected $id = self::BEACON;
	
	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}
	
	public function getName() : String {
		return "Beacon";
	}
	
	public function getHardness() : float {
		return 3;
	}
	
	public function getLightLevel() : int {
		return 15;
	}
	
	public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null) : bool {
		$this->getLevel()->setBlock($blockReplace, $this, true, true);
		
		Tile::createTile(Tile::BEACON, $this->getLevel(), TileBeacon::createNBT($this, $face, $item, $player));
		
		return true;
	}
	
	public function onActivate(Item $item, Player $player = null) : bool {
		if($player instanceof Player) {
			$tile = $this->level->getTile($this);
			
			if($tile instanceof TileBeacon)
				$player->addWindow($tile->getInventory());
		}
		
		return true;
	}
}