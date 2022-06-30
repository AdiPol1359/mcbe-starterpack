<?php

namespace core\fakeinventory\inventory;

use core\manager\managers\LogManager;
use core\manager\managers\MagicCaseManager;
use core\fakeinventory\FakeInventory;
use core\manager\managers\ParticlesManager;
use core\manager\managers\SoundManager;
use core\task\tasks\DrawTask;
use core\util\utils\InventoryUtil;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use pocketmine\level\sound\ClickSound;

class MagicCaseInventory extends FakeInventory {

    private DrawTask $task;

    private int $time = 0;
    private int $stop = 0;

    public function __construct(Player $player) {
        parent::__construct($player, "§9§lTrwa otwieranie§8...", self::SMALL);
        $this->player = $player;
        $this->setItems();
    }

    private function setItems() : void {
        for($i = 0; $i < $this->getSize(); $i++)
            $this->setItem($i, Item::get(Item::IRON_BARS)->setCustomName(" "));

        $this->setItem(4, Item::get(Item::HOPPER)->setCustomName(" "));
        $this->setItem(22, Item::get(Item::LEVER)->setCustomName(" "));

        for($i = 9; $i <= 17; $i++)
            $this->setItem($i, MagicCaseManager::getRandomItem());
    }

    public function setTask(DrawTask $task) : void {
        $this->task = $task;
    }

    public function getTask() : DrawTask {
        return $this->task;
    }

    public function stop() : void {
        $this->task->getHandler()->cancel();
        MagicCaseManager::removeOpeningMagicCase($this->player);
    }

    public function update() : void {
        if(!$this->player->isOnline()) {
            $this->stop();
            return;
        }

        $this->time++;

        if($this->time >= 15 && $this->time < 25) {
            if($this->stop == 0)
                $this->stop = 1;
            else {
                $this->stop--;
                return;
            }
        } elseif($this->time >= 25) {
            if($this->stop == 0)
                $this->stop = 2;
            else {
                $this->stop--;
                return;
            }

            if($this->time >= 35) {
                for($s = 1; $s <= 9; $s++) {
                    if($s === 5) {
                        continue;
                    }

                    $slot = 9 + $s - 1;

                    $this->setItem($slot, Item::get(Item::IRON_BARS)->setCustomName(" "));
                }

                for($i = 0; $i < $this->getSize(); $i++) {
                    if($this->getItem($i)->getId() === Item::AIR)
                        $this->setItem($i, Item::get(Item::IRON_BARS)->setCustomName(" "));
                }

                if($this->time >= 42) {
                    for($s = 1; $s <= 9; $s++) {
                        if($s === 5) {
                            $slot = 9 + $s - 1;
                            $item = $this->getItem($slot);

                            InventoryUtil::addItem($item, $this->player);
                            LogManager::sendLog($this->player, "OpenResult: " . $item->getId() . ":" . $item->getDamage() . ":" . $item->getCount(), LogManager::MAGIC_CASE);

                        }
                    }

                    $this->stop();
                    $this->closeFor($this->player);
                    $this->player->getLevel()->broadcastLevelSoundEvent($this->player, LevelSoundEventPacket::SOUND_BLAST);
                    ParticlesManager::spawnFirework($this->player, $this->player->getLevel(), [[ParticlesManager::TYPE_STAR, ParticlesManager::COLOR_YELLOW], [ParticlesManager::TYPE_STAR, ParticlesManager::COLOR_GOLD]]);
                }
                return;
            }
        }

        for($s = 1; $s <= 9; $s++) {
            if($s === 9) {
                $this->setItem(17, MagicCaseManager::getRandomItem());
                continue;
            }

            $slot = 9 + $s - 1;

            $this->setItem($slot, $this->getItem($slot+1));
        }

        $this->player->getLevel()->addSound(new ClickSound($this->player), [$this->player]);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {
        return true;
    }

    public function onClose(Player $who) : void {

        if($this->time <= 20) {
            $this->stop();

            $item = MagicCaseManager::getRandomItem();

            InventoryUtil::addItem($item, $this->player);
            LogManager::sendLog($this->player, "OpenResult: " . $item->getId() . ":" . $item->getDamage() . ":" . $item->getCount(), LogManager::MAGIC_CASE);

            $this->player->getLevel()->broadcastLevelSoundEvent($this->player, LevelSoundEventPacket::SOUND_BLAST);
            ParticlesManager::spawnFirework($this->player, $this->player->getLevel(), [[ParticlesManager::TYPE_STAR, ParticlesManager::COLOR_YELLOW], [ParticlesManager::TYPE_STAR, ParticlesManager::COLOR_GOLD]]);
        }

        parent::onClose($who);
    }
}