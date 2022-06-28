<?php

namespace Gildie\fakeinventory;

use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\item\Item;
use pocketmine\block\BlockFactory;
use pocketmine\inventory\ContainerInventory;
use pocketmine\nbt\{
	NetworkLittleEndianNBTStream, tag\CompoundTag
};
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;

class FakeInventory extends ContainerInventory {
	
	protected $player;
	protected $holder;
	protected $title;
	protected $size;
	protected $type;
	protected $cancelTransaction = false;
	
	public function __construct(Player $player, string $title = "Fake Inventory") {
		
		$holder = $player->floor()->add(0, 3);
		$size = 27;
		
		parent::__construct($holder, [], $size, $title);
		
		$this->player = $player;
		$this->holder = $holder;
		$this->title = $title;
		$this->size = $size;
	}

    public function send() : void {

        $pos = $this->player->floor()->add(0,3);

        $pk = new UpdateBlockPacket();
        $pk->x = $pos->x;
        $pk->y = $pos->y;
        $pk->z = $pos->z;
        $pk->flags = UpdateBlockPacket::FLAG_ALL;
        $pk->blockRuntimeId = BlockFactory::toStaticRuntimeId(54);

        $this->player->dataPacket($pk);

        $writer = new NetworkLittleEndianNBTStream();

        $pk = new BlockActorDataPacket;
        $pk->x = $pos->x;
        $pk->y = $pos->y;
        $pk->z = $pos->z;

        $tag = new CompoundTag();
        $tag->setString('CustomName', $this->title);

        $pk->namedtag = $writer->write($tag);

        $this->player->dataPacket($pk);

        $this->player->addWindow($this);

        FakeInventoryAPI::setInventory($this->player, $this);
    }

    public function closeWindow() : void {
        $pos = $this->holder;

        $block = $this->player->getLevel()->getBlock($pos);

        $pk = new UpdateBlockPacket();
        $pk->x = $pos->x;
        $pk->y = $pos->y;
        $pk->z = $pos->z;
        $pk->flags = UpdateBlockPacket::FLAG_ALL;
        $pk->blockRuntimeId = BlockFactory::toStaticRuntimeId($block->getId(), $block->getDamage());

        $this->player->dataPacket($pk);
        FakeInventoryAPI::unsetInventory($this->player);
    }

    public function onClose(Player $who) : void {
        $this->closeWindow();
        parent::onClose($who);
    }

    public function getNetworkType() : int {
        return WindowTypes::CONTAINER;
    }

    public function getName() : string {
        return $this->title;
    }

    public function getDefaultSize() : int {
        return $this->size;
    }

    public function getPlayer() : Player {
        return $this->player;
    }

    public function getHolder() : Vector3 {
        return $this->holder;
    }

    public function onTransaction(Player $player, ?Item $item) : void {

    }

    public function cancelTransaction() : bool {
        return $this->cancelTransaction;
    }

    public function setCancelTransaction(bool $status = true) : void {
        $this->cancelTransaction = $status;
    }
}