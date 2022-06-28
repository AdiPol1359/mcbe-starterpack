<?php

namespace Core\entity;

use pocketmine\entity\Creature;

use pocketmine\Player;

use pocketmine\item\Item;

use pocketmine\math\Vector3;

use pocketmine\nbt\tag\{CompoundTag, ListTag, StringTag, IntTag, ByteTag};

use pocketmine\nbt\NetworkLittleEndianNBTStream;

use Core\inventory\VillagerInventory;

class Villager extends Creature {

	public const NETWORK_ID = self::VILLAGER;

	public $width = 0.6;
	public $height = 1.8;

	public function getName() : string {
		return "Villager";
	}
	
	protected function initEntity() : void {
		parent::initEntity();
		
		$recipesTag = $this->namedtag->getCompoundTag("Offers");
		
		if($recipesTag == null)
		 $this->namedtag->setTag(new CompoundTag("Offers", [
			new ListTag("Recipes", [])
		]));
	}
	
	public function addRecipe(Item $buyA, Item $buyB = null, Item $sell) : void {
		if($buyB == null){
			$recipe = new CompoundTag("", [
			 $buyA->nbtSerialize(-1, "buyA"),
			 $sell->nbtSerialize(-1, "sell"),
			 new IntTag("maxUses", 99999),
			 new IntTag("uses", 0),
				new ByteTag("rewardExp", 0)
			]);
		}else{
 		$recipe = new CompoundTag("", [
			 $buyA->nbtSerialize(-1, "buyA"),
			 $buyB->nbtSerialize(-1, "buyB"),
			 $sell->nbtSerialize(-1, "sell"),
			 new IntTag("maxUses", 99999),
			 new IntTag("uses", 0),
				new ByteTag("rewardExp", 0)
			]);	
		}
		
		$recipesTag = $this->getRecipes();
		$recipesTag->push($recipe);
		
		$this->namedtag->setTag(new CompoundTag("Offers", [
			$recipesTag
		]));
	}
	
	public function removeRecipe(int $index) : void {
		$tag = $this->getRecipes();
		
		$newTag = new ListTag("Recipes", []);
		
		foreach($tag as $key => $value){
			if($key !== $index){
				$newTag->push($value);
			}
		}
		
		$this->namedtag->setTag(new CompoundTag("Offers", [
			$newTag
		]));
	}
	
	public function getOffersTag() : CompoundTag {
		return $this->namedtag->getTag("Offers");
	}
	
	public function getOffers() : String {
		$writer = new NetworkLittleEndianNBTStream();
		
		return $writer->write($this->getOffersTag());
	}
	
	public function getRecipes() : ListTag {
		return $this->getOffersTag()->getListTag("Recipes");
	}
	
	public function getCustomName() : string {
		if($this->namedtag->hasTag("CustomName", StringTag::class))
			return $this->namedtag->getString("CustomName");
			
			return $this->getName();
	}
	
	public function setCustomName(string $customName) : void {
		$this->namedtag->setString("CustomName", $customName);
	}
	
	public function onInteract(Player $player, Item $item, Vector3 $clickPos) : bool {
		
		$player->addWindow(new VillagerInventory($this));
		
		return parent::onInteract($player, $item, $clickPos);
	}
}