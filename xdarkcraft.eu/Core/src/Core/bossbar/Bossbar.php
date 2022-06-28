<?php

namespace Core\bossbar;

use pocketmine\Player;

use pocketmine\math\Vector3;

use pocketmine\entity\{
	Entity, Attribute
};

use pocketmine\network\mcpe\protocol\{
	AddActorPacket,
	AddEntityPacket,
	BossEventPacket,
	RemoveActorPacket,
	RemoveEntityPacket,
	SetActorDataPacket,
	UpdateAttributesPacket
};

class Bossbar extends Vector3 {
	
	private $healthPercent = 0;
	private $entityId;
	private $metadata = [];
	private $viewers = [];

	public function __construct(string $title = "Bossbar", float $hp = 1.0) {
		parent::__construct(0, 0, 0);

		$flags = ((1 << Entity::DATA_FLAG_INVISIBLE) | (1 << Entity::DATA_FLAG_IMMOBILE));
		$this->metadata = [
			Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, $flags],
			Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $title]
		];

		$this->entityId = Entity::$entityCount++;

		$this->setHealthPercent($hp);
	}

	public function setTitle(string $t, bool $update = true) : void {
		$this->setMetadata(Entity::DATA_NAMETAG, Entity::DATA_TYPE_STRING, $t);
		if($update){
			$this->updateForAll();
		}
	}

	public function getTitle() : string {
		return $this->getMetadata(Entity::DATA_NAMETAG);
	}
	
	public function setHealthPercent(float $hp, bool $update = true) : void {
		$this->healthPercent = max(0, min(1.0, $hp));

		if($update){
			$this->updateForAll();
		}
	}

	public function getHealthPercent() : float {
		return $this->healthPercent;
	}

	public function showTo(Player $player, bool $isViewer = true) : void {
		$pk = new AddActorPacket();
		$pk->entityRuntimeId = $this->entityId;
		$pk->type = Entity::SLIME;
		$pk->metadata = $this->metadata;
		$pk->position = $this;

		$player->sendDataPacket($pk);
		$player->sendDataPacket($this->getHealthPacket());

		$this->sendBossEventPacket($player, BossEventPacket::TYPE_SHOW);

		if($isViewer){
			$this->viewers[spl_object_id($player)] = $player;
		}
		
		BossbarManager::setBossbar($player, $this);
	}

	public function hideFrom(Player $player) : void {
		$this->sendBossEventPacket($player, BossEventPacket::TYPE_HIDE);

		$pk2 = new RemoveActorPacket();
		$pk2->entityUniqueId = $this->entityId;

		$player->sendDataPacket($pk2);

		if(isset($this->viewers[spl_object_id($player)])){
			unset($this->viewers[spl_object_id($player)]);
		}
		
		BossbarManager::unsetBossbar($player);
	}
	
	public function hideFromAll() : void {
		foreach($this->viewers as $player){
			$this->hideFrom($player);
		}
	}

	public function updateFor(Player $player) : void {
		$this->hideFrom($player);
		$this->showTo($player);
	}

	public function updateForAll() : void {
		foreach($this->viewers as $player){
			$this->updateFor($player);
		}
	}

	private function getHealthPacket() : UpdateAttributesPacket {
		$attr = Attribute::getAttribute(Attribute::HEALTH);
		$attr->setMaxValue(1.0);
		$attr->setValue($this->healthPercent);

		$pk = new UpdateAttributesPacket();
		$pk->entityRuntimeId = $this->entityId;
		$pk->entries = [$attr];

		return $pk;
	}

	private function sendBossEventPacket(Player $player, int $eventType) : void {
		$pk = new BossEventPacket();
		$pk->bossEid = $this->entityId;
		$pk->eventType = $eventType;

		switch($eventType){
			case BossEventPacket::TYPE_SHOW:
				$pk->title = $this->getTitle();
				$pk->healthPercent = $this->healthPercent;
				$pk->color = 0;
				$pk->overlay = 0;
				$pk->unknownShort = 0;
				break;
			case BossEventPacket::TYPE_REGISTER_PLAYER:
			case BossEventPacket::TYPE_UNREGISTER_PLAYER:
				$pk->playerEid = $player->getId();
				break;
			case BossEventPacket::TYPE_TITLE:
				$pk->title = $this->getTitle();
				break;
			case BossEventPacket::TYPE_HEALTH_PERCENT:
				$pk->healthPercent = $this->getHealthPercent();
				break;
		}

		$player->sendDataPacket($pk);
	}

	public function setMetadata(int $key, int $dtype, $value) : void {
		$this->metadata[$key] = [$dtype, $value];
	}
	
	public function getMetadata(int $key) {
		return isset($this->metadata[$key]) ? $this->metadata[$key][1] : null;
	}

	public function getViewers() : array {
		return $this->viewers;
	}

	public function getEntityId() : int {
		return $this->entityId;
	}
}