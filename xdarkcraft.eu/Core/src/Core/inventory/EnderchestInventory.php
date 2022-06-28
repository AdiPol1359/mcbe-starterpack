<?php

namespace Core\inventory;

use pocketmine\Player;

use pocketmine\math\Vector3;

use pocketmine\block\BlockFactory;

use pocketmine\inventory\ContainerInventory;

use pocketmine\nbt\{
	NBT, tag\ListTag
};

use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;

use pocketmine\network\mcpe\protocol\types\WindowTypes;

use pocketmine\item\Item;

use Core\Main;

class EnderchestInventory extends ContainerInventory {
	
	public const SIZE_SMALL = 27;
	public const SIZE_LARGE = 54;
	
	public const TITLE = "§l§4Enderchest";
	
	protected $holder;
	protected $pos;
	protected $size;
	
	public function __construct(Player $player, ?Vector3 $pos, int $size = self::SIZE_SMALL) {
		
		$holder = $player->floor()->add(0, 3);
		
		$items = [];
		
		if($player->namedtag->getListTag("ECInventory") == null)
		 $player->namedtag->setTag(new ListTag("ECInventory", [], NBT::TAG_Compound));
		
		$enderChestInventoryTag = $player->namedtag->getListTag("ECInventory");
		if($enderChestInventoryTag !== null){
			foreach($enderChestInventoryTag as $i => $item){
				$items[$item->getByte("Slot")] = Item::nbtDeserialize($item);
			}
		}
		
		parent::__construct($holder, $items, $size, self::TITLE);
		
		$this->holder = $holder;
		$this->pos = $pos;
		$this->size = $size;
	}
	
	public function onOpen(Player $who) : void {
		Main::$ec[$who->getName()] = $this;
		
		parent::onOpen($who);
	}

 public function onClose(Player $who) : void {
 	
 	unset(Main::$ec[$who->getName()]);
 	
 	$pos = $this->holder;
 	
 	$block = $who->getLevel()->getBlock($pos);
 	
 	$pk = new UpdateBlockPacket();
  $pk->x = $pos->x;
  $pk->y = $pos->y;
  $pk->z = $pos->z;
  $pk->flags = UpdateBlockPacket::FLAG_ALL;
  $pk->blockRuntimeId = BlockFactory::toStaticRuntimeId($block->getId(), $block->getDamage());
  
  $who->dataPacket($pk);
  
  if($this->size == self::SIZE_LARGE) {
  	$pos = $pos->add(1);
  	$pk = new UpdateBlockPacket();
   $pk->x = $pos->x;
   $pk->y = $pos->y;
   $pk->z = $pos->z;
   $pk->flags = UpdateBlockPacket::FLAG_ALL;
   $pk->blockRuntimeId = BlockFactory::toStaticRuntimeId($block->getId(), $block->getDamage());
  	$who->dataPacket($pk);
  }
  
  if($this->pos !== null) {
  	$pos = $this->pos;
   $pk = new BlockEventPacket();
	 	$pk->x = $pos->x;
	  $pk->y = $pos->y;
	 	$pk->z = $pos->z;
	 	$pk->eventType = 1; 
	 	$pk->eventData = 0;
	 	
		 $who->dataPacket($pk);
  
   $who->getLevel()->broadcastLevelSoundEvent($pos->add(0.5, 0.5, 0.5), LevelSoundEventPacket::SOUND_ENDERCHEST_CLOSED);
 	}
 	
		$items = [];

		$slotCount = $this->getDefaultSize();
		
		for($slot = 0; $slot < $slotCount; ++$slot){
			$item = $this->getItem($slot);
			if(!$item->isNull()){
				$items[] = $item->nbtSerialize($slot);
			}
		}

		$who->namedtag->setTag(new ListTag("ECInventory", $items, NBT::TAG_Compound));
 	
 	parent::onClose($who);
 }
	
	public function getNetworkType() : int {
	 return WindowTypes::CONTAINER;
 }
    
 public function getName() : string {
  return self::TITLE;
 }
    
 public function getDefaultSize() : int{
  return $this->size;
 }

 public function getHolder() : Vector3 {
  return $this->holder;
 }
}