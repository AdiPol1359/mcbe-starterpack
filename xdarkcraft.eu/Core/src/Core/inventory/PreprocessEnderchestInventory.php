<?php

namespace Core\inventory;

use pocketmine\Player;

use pocketmine\math\Vector3;

use pocketmine\block\BlockFactory;

use pocketmine\nbt\{
	NetworkLittleEndianNBTStream, tag\CompoundTag
};

use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\BlockEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;

use Core\Main;

use Core\task\OpenEnderchestTask;

class PreprocessEnderchestInventory {
	
	public function __construct(Player $player, ?Vector3 $pos, int $size = EnderchestInventory::SIZE_SMALL) {
		if(isset(Main::$ec[$player->getName()])) {
		 $inv = new EnderchestInventory($player, $pos, $size);
		 $inv->onClose($player);
		}
		$this->sendPackets($player, $pos, $size);
	}
	
	public function sendPackets(Player $who, ?Vector3 $ecPos, int $size) : void {
 	
 	$pos = $who->floor()->add(0, 3);
 	
  $pk = new UpdateBlockPacket();
  $pk->x = $pos->x;
  $pk->y = $pos->y;
  $pk->z = $pos->z;
  $pk->flags = UpdateBlockPacket::FLAG_ALL;
  $pk->blockRuntimeId = BlockFactory::toStaticRuntimeId(54);
  
  $who->dataPacket($pk);
  
  if($size == EnderchestInventory::SIZE_LARGE) {
  	$pos1 = $pos->add(1);
   	
  	$pk = new UpdateBlockPacket();
   $pk->x = $pos1->x;
   $pk->y = $pos1->y;
   $pk->z = $pos1->z;
   $pk->flags = UpdateBlockPacket::FLAG_ALL;
   $pk->blockRuntimeId = BlockFactory::toStaticRuntimeId(54);
  
   $who->dataPacket($pk);
			
			$tag = new CompoundTag();
   $tag->setInt('pairx', $pos->x);
   $tag->setInt('pairz', $pos->z);
   
   $writer = new NetworkLittleEndianNBTStream();
   $pk = new BlockActorDataPacket;
   $pk->x = $pos1->x;
   $pk->y = $pos1->y;
   $pk->z = $pos1->z;
   $pk->namedtag = $writer->write($tag);
   $who->dataPacket($pk);
  }
  
  $writer = new NetworkLittleEndianNBTStream();
  
  $pk = new BlockActorDataPacket;
  $pk->x = $pos->x;
  $pk->y = $pos->y;
  $pk->z = $pos->z;

  $tag = new CompoundTag();
  $tag->setString('CustomName', EnderchestInventory::TITLE);

  $pk->namedtag = $writer->write($tag);
  
  $who->dataPacket($pk);
  
  if($ecPos !== null) {
   $pk = new BlockEventPacket();
 		$pk->x = $ecPos->x;
 		$pk->y = $ecPos->y;
		 $pk->z = $ecPos->z;
		 $pk->eventType = 1; 
		 $pk->eventData = 1;
		 
	 	$who->dataPacket($pk);
   
   $who->getLevel()->broadcastLevelSoundEvent($ecPos->add(0.5, 0.5, 0.5), LevelSoundEventPacket::SOUND_ENDERCHEST_OPEN);
  }
  
  Main::getInstance()->getScheduler()->scheduleDelayedTask(new OpenEnderchestTask($who, $ecPos, $size), 5);
 }
}