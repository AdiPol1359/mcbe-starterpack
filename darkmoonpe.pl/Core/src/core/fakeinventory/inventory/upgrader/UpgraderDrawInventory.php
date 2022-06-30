<?php

namespace core\fakeinventory\inventory\upgrader;

use core\fakeinventory\FakeInventory;
use core\manager\managers\LogManager;
use core\manager\managers\SoundManager;
use core\manager\managers\UpgradeManager;
use core\manager\managers\ParticlesManager;
use core\task\tasks\DrawTask;
use core\util\utils\InventoryUtil;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;
use pocketmine\level\sound\ClickSound;

class UpgraderDrawInventory extends FakeInventory {

    private DrawTask $task;
    private static Item $oldPickaxe;

    private int $time = 0;
    private int $stop = 0;

    public function __construct(Player $player, Item $pickaxe) {
        parent::__construct($player, "§9§lTrwa upgradeowanie§8...", self::SMALL);
        $this->player = $player;

        $pickaxe->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), 6));

        self::$oldPickaxe = $pickaxe;

        $this->setItems();
    }

    private function setItems() : void {
        for($i = 0; $i < $this->getSize(); $i++)
            $this->setItem($i, Item::get(Item::IRON_BARS)->setCustomName(" "));

        $this->setItem(4, Item::get(Item::HOPPER)->setCustomName(" "));
        $this->setItem(22, Item::get(Item::LEVER)->setCustomName(" "));

        for($i = 9; $i <= 17; $i++)
            $this->setItem($i, self::getRandomItem());
    }

    public static function getRandomItem() : Item {

        $items = [];

        $burn = Item::get(Item::FIRE)->setCustomName("§l§cSPALONY!");

        foreach([70 => $burn, 30 => self::$oldPickaxe] as $chance => $item)
            $items[] = [$item, $chance];

        foreach($items as $drop) {
            if((mt_rand(1,10000)/100) <= $drop[1])
                return $drop[0];
        }

        return self::getRandomItem();
    }

    public function setTask(DrawTask $task) : void {
        $this->task = $task;
    }

    public function getTask() : DrawTask {
        return $this->task;
    }

    public function stop() : void {
        $this->task->getHandler()->cancel();
        UpgradeManager::removeOpeningUpgradeDraw($this->player);
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

                $resultItem = null;

                if($this->time >= 42) {
                    for($s = 1; $s <= 9; $s++) {
                        if($s === 5) {
                            $slot = 9 + $s - 1;
                            $item = $this->getItem($slot);

                            if($item->getId() !== Item::FIRE)
                                InventoryUtil::addItem($item, $this->player);

                            $resultItem = $item;
                        }
                    }

                    $this->stop();
                    $this->closeFor($this->player);
                    $this->player->getLevel()->broadcastLevelSoundEvent($this->player, LevelSoundEventPacket::SOUND_BLAST);
                    LogManager::sendLog($this->player, "UpgradeResult: ".$resultItem->getId().":".$resultItem->getDamage().":".$resultItem->getCount(), LogManager::BLACK_SMITH);

                    if($resultItem instanceof Tool)
                        ParticlesManager::spawnFirework($this->player, $this->player->getLevel(), [[ParticlesManager::TYPE_HUGE_SPHERE, ParticlesManager::COLOR_DARK_PURPLE], [ParticlesManager::TYPE_HUGE_SPHERE, ParticlesManager::COLOR_BLUE]]);
                }
                return;
            }
        }

        for($s = 1; $s <= 9; $s++) {
            if($s === 9) {
                $this->setItem(17, self::getRandomItem());
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
}