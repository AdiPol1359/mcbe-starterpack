<?php

namespace core\fakeinventory\inventory;

use core\fakeinventory\FakeInventory;
use core\Main;
use core\manager\managers\PacketManager;
use core\manager\managers\ParticlesManager;
use core\manager\managers\SoundManager;
use core\task\tasks\TradeTask;
use pocketmine\item\Item;
use pocketmine\Player;

class TradeInventory extends FakeInventory {

    public Player $owner;
    public Player $tradePlayer;

    public bool $readySender = false;
    public bool $readyPlayer = false;

    public bool $correctEnd = false;

    public ?int $task = null;
    public int $count = 5;

    private array $border = [4, 13, 22, 31, 40, 49];
    public array $player1area = [0, 1, 2, 3, 9, 10, 11, 12, 18, 19, 20, 21, 27, 28, 29, 30, 36, 37, 38, 39, 46, 47, 48];
    public array $player2area = [5, 6, 7, 8, 14, 15, 16, 17, 23, 24, 25, 26, 32, 33, 34, 35, 41, 42, 43, 44, 50, 51, 52];

    public function __construct(Player $sender, Player $player) {

        $this->owner = $sender;
        $this->tradePlayer = $player;
        $this->readySender = false;
        $this->readyPlayer = false;
        $this->count = 5;

        $senderName = strlen($sender->getName()) > 8 ? substr($sender->getName(), 0, 8) . "..." : $sender->getName();
        $playerName = strlen($player->getName()) > 8 ? substr($player->getName(), 0, 8) . "..." : $player->getName();

        parent::__construct($player, "§8" . $senderName . str_repeat(" ", 11) . $playerName, self::BIG, false);

        $this->setCancelTransaction(false);
        $this->setItems();
    }

    private function setItems() : void {

        $buttons = [45, 53];
        $middleLines = [4, 13, 22, 31, 40, 49];

        foreach($buttons as $button)
            $this->setItem($button, Item::get(236, 14)->setCustomName("§l§cNIE GOTOWY"));

        foreach($middleLines as $middleLine)
            $this->setItem($middleLine, Item::get(241)->setCustomName(" "));

    }

    public function isReady() : bool {
        return $this->readySender && $this->readyPlayer;
    }

    public function startCounting() : void {
        if(!$this->isReady())
            return;

        foreach($this->border as $borderSlots)
            $this->setItem($borderSlots, Item::get(241, 4, 5));

        foreach([$this->tradePlayer, $this->owner] as $tradePlayer)
            SoundManager::addSound($tradePlayer, $this->holder, "random.click");

        $this->task = Main::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new TradeTask($this), 20, 20)->getTaskId();
    }

    public function countDown() : void {

        if(!$this->isReady()) {
            $this->resetCountDown();
            return;
        }

        $this->count--;

        foreach($this->border as $borderSlots) {
            if($this->count === 5)
                $this->setItem($borderSlots, Item::get(241, 4, 5));

            if($this->count === 4)
                $this->setItem($borderSlots, Item::get(241, 4, 4));

            if($this->count === 3)
                $this->setItem($borderSlots, Item::get(241, 1, 3));

            if($this->count === 2)
                $this->setItem($borderSlots, Item::get(241, 14, 2));

            if($this->count === 1)
                $this->setItem($borderSlots, Item::get(241, 15));

            foreach([$this->tradePlayer, $this->owner] as $tradePlayer)
                SoundManager::addSound($tradePlayer, $this->holder, "random.click");
        }

        if($this->count === 0) {

            $senderItems = [];
            $playerItems = [];

            foreach($this->getContents(false) as $slot => $item) {
                if(in_array($slot, $this->player1area))
                    $playerItems[] = $item;

                if(in_array($slot, $this->player2area))
                    $senderItems[] = $item;
            }

            foreach($senderItems as $senderItem)
                $this->owner->getInventory()->addItem($senderItem);

            foreach($playerItems as $playerItem)
                $this->tradePlayer->getInventory()->addItem($playerItem);

            $this->owner->addTitle("§l§aWYMIANA ZAKONCZONA!");
            $this->tradePlayer->addTitle("§l§aWYMIANA ZAKONCZONA!");

            foreach([$this->tradePlayer, $this->owner] as $tradePlayer)
                ParticlesManager::spawnFirework($tradePlayer, $tradePlayer->getLevel(), [[ParticlesManager::TYPE_HUGE_SPHERE, ParticlesManager::COLOR_GREEN], [ParticlesManager::TYPE_HUGE_SPHERE, ParticlesManager::COLOR_GREEN]]);

            Main::getInstance()->getScheduler()->cancelTask($this->task);

            $this->closeFor($this->owner);
            $this->closeFor($this->tradePlayer);
            $this->task = null;
            $this->correctEnd = true;
        }
    }

    public function resetCountDown() : void {
        foreach($this->border as $borderSlots)
            $this->setItem($borderSlots, Item::get(241));

        $this->count = 5;
        Main::getInstance()->getScheduler()->cancelTask($this->task);
    }

    public function isCounting() : bool {
        if(is_null($this->task))
            return false;

        return Main::getInstance()->getScheduler()->isQueued($this->task);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== Item::IRON_BARS)
            SoundManager::addSound($player, $this->holder, "random.click");

        $this->cancelTransaction = false;

        if(!in_array($slot, $this->player1area) && !in_array($slot, $this->player2area))
            $this->cancelTransaction = true;

        if(in_array($slot, $this->player1area) && $this->owner !== $player || in_array($slot, $this->player2area) && $this->tradePlayer !== $player)
            $this->cancelTransaction = true;

        if($this->owner === $player && $this->readySender || $this->tradePlayer === $player && $this->readyPlayer)
            $this->cancelTransaction = true;

        if($slot === 45) {

            if($this->owner !== $player)
                return true;

            switch($sourceItem->getDamage()) {
                case 5:
                    if($this->isCounting())
                        $this->resetCountDown();
                    $this->readySender = false;
                    $sourceItem->setCustomName("§l§cNIE GOTOWY");
                    $sourceItem->setDamage(14);
                    $this->setItem(45, $sourceItem);
                    break;
                case 14:
                    $this->readySender = true;
                    $sourceItem->setCustomName("§l§aGOTOWY");
                    $sourceItem->setDamage(5);
                    $this->setItem(45, $sourceItem);
                    if(!$this->isCounting())
                        $this->startCounting();
                    break;
            }
        }

        if($slot === 53) {

            if($this->tradePlayer !== $player)
                return true;

            switch($sourceItem->getDamage()) {
                case 5:
                    if($this->isCounting())
                        $this->resetCountDown();
                    $this->readyPlayer = false;
                    $sourceItem->setCustomName("§l§cNIE GOTOWY");
                    $sourceItem->setDamage(14);
                    $this->setItem(53, $sourceItem);
                    break;
                case 14:
                    $this->readyPlayer = true;
                    $sourceItem->setCustomName("§l§aGOTOWY");
                    $sourceItem->setDamage(5);
                    $this->setItem(53, $sourceItem);
                    if(!$this->isCounting())
                        $this->startCounting();
                    break;
            }
        }

        if($this->cancelTransaction)
            PacketManager::unClickButton($player);

        return $this->cancelTransaction;
    }

    public function onClose(Player $who) : void {

        if($this->isCounting())
            $this->resetCountDown();

        $items = $this->getContents(false);

        $senderItems = [];
        $playerItems = [];

        foreach($items as $slot => $item) {
            if(in_array($slot, $this->player1area))
                $senderItems[] = $item;

            if(in_array($slot, $this->player2area))
                $playerItems[] = $item;
        }

        if(!$this->correctEnd) {
            switch($who->getName()) {
                case $this->owner->getName():
                    foreach($senderItems as $senderItem) {
                        if(!$this->owner)
                            break;

                        $this->owner->getInventory()->addItem($senderItem);
                    }
                    break;

                case $this->tradePlayer->getName():
                    foreach($playerItems as $playerItem) {
                        if(!$this->tradePlayer)
                            break;

                        $this->tradePlayer->getInventory()->addItem($playerItem);
                    }
                    break;

            }

            $who->addTitle("§l§cANULOWANO WYMIANE!");
        }

        foreach([$this->owner, $this->tradePlayer] as $tradePlayer){
            if(!$tradePlayer)
                continue;

            $this->closeFor($tradePlayer);
        }

        parent::onClose($who);
    }
}