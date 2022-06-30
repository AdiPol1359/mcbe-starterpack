<?php

namespace core\fakeinventory;

use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\block\BlockFactory;
use pocketmine\inventory\ContainerInventory;
use pocketmine\nbt\{
	NetworkLittleEndianNBTStream, tag\CompoundTag
};
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use core\task\tasks\SendFakeInventoryWindowTask;
use core\Main;

abstract class FakeInventory extends ContainerInventory {

    public const SMALL = 27;
    public const BIG = 54;

    protected $holder;
    protected $title;
    protected int $size;
    protected ?Player $player;
    protected bool $cancelTransaction = false;
    protected bool $isClosed = false;
    protected bool $inventoryBehindPlayer;

    private static array $chest = [];

    public function __construct(Player $player = null, string $title = "Fake Inventory", int $size = 54, $inventoryBehindPlayer = true) {
        $holder = new Vector3();

        parent::__construct($holder, [], $size, $title);

        $this->player = $player;
        $this->holder = $holder;
        $this->title = $title;
        $this->size = $size;
        $this->inventoryBehindPlayer = $inventoryBehindPlayer;
    }

    private function getBlockBehindPlayer(Player $player) : Vector3{
        switch($player->getDirection()) {
            case 0:
                return $player->asVector3()->floor()->subtract(2);
            case 1:
                return $player->asVector3()->floor()->subtract(0, 0, 2);
            case 2:
                return $player->asVector3()->floor()->add(2);
            case 3;
                return $player->asVector3()->floor()->add(0, 0, 2);
            default:
                return $player->asVector3()->floor();
        }
    }

    public function openFor(array $players) : void {

        foreach($players as $player) {
            if(FakeInventoryAPI::isOpening($player))
                $this->closeFor($player);
        }

        $playerPos = new Vector3();

        if(count($players) <= 1) {
            foreach($players as $player) {
                $playerPos = $this->getBlockBehindPlayer($player);

                if(!$this->inventoryBehindPlayer)
                    $playerPos = $player->asVector3()->floor();
            }
        }else{
            $x = round(($players[0]->x + $players[1]->x) / 2);
            $y = round(($players[0]->y + $players[1]->y) / 2);
            $z = round(($players[0]->z + $players[1]->z) / 2);

            $playerPos = (new Vector3($x, $y, $z))->floor();
        }

        $pos = $playerPos->add(0, 2);

        $this->holder = new Vector3($pos->x, $pos->y, $pos->z);

        $pk = new UpdateBlockPacket();
        $pk->x = $pos->x;
        $pk->y = $pos->y;
        $pk->z = $pos->z;
        $pk->flags = UpdateBlockPacket::FLAG_ALL;
        $pk->blockRuntimeId = BlockFactory::toStaticRuntimeId(54);

        foreach($players as $player) {
            $player->dataPacket($pk);
            self::$chest[$player->getName()] = [];
            self::$chest[$player->getName()][] = $pos;
        }

        if($this->size == self::BIG) {
            $pairPos = $pos->add(1);

            $pkBlock = new UpdateBlockPacket();
            $pkBlock->x = $pairPos->x;
            $pkBlock->y = $pairPos->y;
            $pkBlock->z = $pairPos->z;
            $pkBlock->flags = UpdateBlockPacket::FLAG_ALL;
            $pkBlock->blockRuntimeId = BlockFactory::toStaticRuntimeId(54);

            $tag = new CompoundTag();
            $tag->setInt('pairx', $pos->x);
            $tag->setInt('pairz', $pos->z);

            $writer = new NetworkLittleEndianNBTStream();
            $pkActor = new BlockActorDataPacket;
            $pkActor->x = $pairPos->x;
            $pkActor->y = $pairPos->y;
            $pkActor->z = $pairPos->z;
            $pkActor->namedtag = $writer->write($tag);

            foreach($players as $player) {
                $player->dataPacket($pkBlock);
                $player->dataPacket($pkActor);
                self::$chest[$player->getName()][] = $pairPos;
            }
        }

        $writer = new NetworkLittleEndianNBTStream();

        $pk = new BlockActorDataPacket;
        $pk->x = $pos->x;
        $pk->y = $pos->y;
        $pk->z = $pos->z;

        $tag = new CompoundTag();
        $tag->setString('CustomName', $this->title);
        $pk->namedtag = $writer->write($tag);

        foreach($players as $player) {
            $player->dataPacket($pk);
            Main::getInstance()->getScheduler()->scheduleDelayedTask(new SendFakeInventoryWindowTask($player, $this), 8);
            FakeInventoryAPI::setInventory($player, $this);
        }
    }

    public function onClose(Player $who) : void {
        $this->closeFor($who);
        parent::onClose($who);

        FakeInventoryAPI::unsetInventory($who);
    }

    public function closeFor(Player $player) : void {

        if(!isset(self::$chest[$player->getName()]))
            return;

        foreach(self::$chest[$player->getName()] as $index => $chestPos){

            $block = $player->getLevel()->getBlock($chestPos);

            $pk1 = new UpdateBlockPacket();
            $pk1->x = $chestPos->x;
            $pk1->y = $chestPos->y;
            $pk1->z = $chestPos->z;
            $pk1->flags = UpdateBlockPacket::FLAG_ALL;
            $pk1->blockRuntimeId = BlockFactory::toStaticRuntimeId($block->getId(), $block->getDamage());

            $player->dataPacket($pk1);

        }

        unset(self::$chest[$player->getName()]);
    }

    public function fillBars(int $itemId = ItemIds::IRON_BARS) : void {
        for($i = 0; $i < $this->getSize(); $i++)
            if($this->isSlotEmpty($i))
                $this->setItem($i, Item::get($itemId)->setCustomName(" "));
    }

    public function setItem(int $index, Item $item, bool $send = true, bool $reset = true) : bool {

        if($reset && $item->getId() !== Item::AIR && $item->getCustomName() !== "")
            $item->setCustomName("§r".$item->getCustomName());

        return parent::setItem($index, $item, $send);
    }

    public function setItemAt(int $x, int $y, Item $item) : void {
        $this->setItem((9 * $y - (9 - $x)) - 1 , $item);
    }

    public function getItemAt(int $x, int $y) : Item{
        return $this->getItem((9 * $y - (9 - $x)) - 1);
    }

    public function getSlotAt(int $x, int $y) : int{
        return (9 * $y - (9 - $x)) - 1;
    }

    public function getNetworkType() : int {
        return WindowTypes::CONTAINER;
    }

    public function getName() : string {
        return "Fake Inventory";
    }

    public function getTitle() : string {
        return $this->title;
    }

    public function setTitle(string $title) : void {
        $this->title = $title;
    }

    public function getDefaultSize() : int {
        return $this->size;
    }

    public function cancelTransaction() : bool{
        return $this->cancelTransaction;
    }

    public function setCancelTransaction(bool $value) : void{
        $this->cancelTransaction = $value;
    }

    public function getHolder() : Vector3 {
        return $this->holder;
    }

    public function isClosed() : bool {
        return $this->isClosed;
    }

    abstract public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool;
}