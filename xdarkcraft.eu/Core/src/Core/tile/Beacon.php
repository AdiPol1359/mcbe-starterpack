<?php

namespace Core\tile;

use pocketmine\Player;
use pocketmine\tile\Spawnable;
use pocketmine\inventory\InventoryHolder;
use pocketmine\Server;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\block\Block;
use pocketmine\math\AxisAlignedBB;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use Core\inventory\BeaconInventory;

class Beacon extends Spawnable implements InventoryHolder {
	
	public const TAG_PRIMARY = "primary";
	public const TAG_SECONDARY = "secondary";
	
	private $primary = 0;
	private $secondary = 0;
	
	protected $blocks = [Block::IRON_BLOCK, Block::GOLD_BLOCK, Block::DIAMOND_BLOCK, Block::EMERALD_BLOCK];
	
	protected $inventory;
	
	public function __construct(Level $level, CompoundTag $nbt){
		parent::__construct($level, $nbt);
		
		$this->scheduleUpdate();
	}
	
	protected function readSaveData(CompoundTag $nbt) : void {
		$this->primary = $nbt->getInt(self::TAG_PRIMARY, 0);
		$this->secondary = $nbt->getInt(self::TAG_SECONDARY, 0);
		
		$this->inventory = new BeaconInventory($this);
	}
	
	protected function writeSaveData(CompoundTag $nbt) : void {
		$nbt->setInt(self::TAG_PRIMARY, $this->primary);
		$nbt->setInt(self::TAG_SECONDARY, $this->secondary);
	}
	
	protected function addAdditionalSpawnData(CompoundTag $nbt) : void {
		$nbt->setInt(self::TAG_PRIMARY, $this->primary);
		$nbt->setInt(self::TAG_SECONDARY, $this->secondary);
	}
	
	public function updateCompoundTag(CompoundTag $nbt, Player $player) : bool {
		if($nbt->getString("id") !== Tile::BEACON){
			return false;
		}

		$this->primary = $nbt->getInt(self::TAG_PRIMARY);
		$this->secondary = $nbt->getInt(self::TAG_SECONDARY);

		return true;
	}
	
	public function getInventory() : BeaconInventory {
		return $this->inventory;
	}
	
	public function getPyramidLevel() : int {
		$level = 0;
		
		for($i = 1; $i <= 4; $i++){
			if($this->checkLayer($i)) $level++;
			 else
			break;
		}
		
		return $level;
	}
	
	public function checkLayer(int $layer) : bool  {
		$pos = $this->asVector3()->add(0, -$layer);
		
		for($x = $pos->x - $layer; $x <= $pos->x + $layer; $x++)
		 for($z = $pos->z - $layer; $z <= $pos->z + $layer; $z++)
		 if(!in_array($this->level->getBlockIdAt($x, $pos->y, $z), $this->blocks))
		 return false;
		 
		 return true;
	}
	
	public function getEffectsDuration(int $level) : int {
		switch($level){
			case 1:
			 return 11;
			break;
			
			case 2:
		 	return 13;
			break;
			
			case 3:
			 return 15;
			break;
			
			case 4:
		 	return 16;
			break;
			
			default:
		 	return 0;
		}
	}
	
	public function getEffectsRange(int $level) : int {
		switch($level){
			case 1:
		 	return 20;
			break;
			
			case 2:
		 	return 30;
			break;
			
			case 3:
		 	return 40;
			break;
			
			case 4:
		 	return 50;
			break;
			
			default:
		 	return 0;
		}
	}
	
	public function onUpdate() : bool {
		if(Server::getInstance()->getTick() % (20 * 3) == 0){
			$level = $this->getPyramidLevel();
			$range = $this->getEffectsRange($level);
			
			$players = new AxisAlignedBB($this->x - $range, 0, $this->z - $range, $this->x + $range, Level::Y_MAX, $this->z + $range);
		}
		return true;
	}
}