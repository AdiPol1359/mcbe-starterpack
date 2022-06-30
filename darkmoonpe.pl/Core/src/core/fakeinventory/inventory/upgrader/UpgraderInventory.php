<?php

namespace core\fakeinventory\inventory\upgrader;

use core\fakeinventory\FakeInventory;
use core\item\items\custom\fragment\fragments\UpgradeFragment;
use core\manager\managers\LogManager;
use core\manager\managers\PacketManager;
use core\manager\managers\ParticlesManager;
use core\manager\managers\SoundManager;
use core\manager\managers\UpgradeManager;
use core\util\utils\InventoryUtil;
use pocketmine\block\Air;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\Pickaxe;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\Player;

class UpgraderInventory extends FakeInventory {

    public function __construct(Player $player) {

        parent::__construct($player, "§l§9UPGRADER", self::SMALL);
        $this->setItems();
    }

    public function setItems() : void{
        $this->fillBars();

        $this->setItemAt(4, 2, Item::get(Item::BOTTLE_O_ENCHANTING)->setCustomName("§l§9INFORMACJE O UPGRADZIE"."\n\n".
            "§l§8» §r§7Wymagania: §l§9Kilof 5/3/3"."\n".
            "§l§8» §r§7Otrzymasz: §l§9Kilof 6/3/3"."\n".
            "§l§8» §r§7Szanse: §l§930%§r"."\n\n".
            "§7Jesli bedziesz posiadal §l§9ODLAMEK UPGRADEOW§r§7"."\n"."bedziesz mial §l§9100%§r§7 szans na upgrade"
        ));

        $this->setItemAt(5,2, Item::get(Item::AIR));
        $this->setItemAt(5, 1, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY MOC GO ULEPSZYC"));
        $this->setItemAt(5, 3, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY MOC GO ULEPSZYC"));
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== Item::IRON_BARS)
            SoundManager::addSound($player, $this->holder, "random.click");

        if($slot === 13){

            if($sourceItem instanceof Pickaxe){
                $this->setItemAt(5, 1, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY MOC GO ULEPSZYC"));
                $this->setItemAt(5, 3, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY MOC GO ULEPSZYC"));
                return false;
            }

            if(!$targetItem instanceof Pickaxe && !$targetItem instanceof Air)
                return true;

            if(!$targetItem->hasEnchantment(Enchantment::EFFICIENCY, 5) || !$targetItem->hasEnchantment(Enchantment::FORTUNE, 3) || !$targetItem->hasEnchantment(Enchantment::UNBREAKING, 3)){
                $this->setItemAt(5, 1, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cTEN ITEM NIE SPELNIA WYMAGAN ABY GO ULEPSZYC"));
                $this->setItemAt(5, 3, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cTEN ITEM NIE SPELNIA WYMAGAN ABY GO ULEPSZYC"));
                return false;
            }

            $this->setItemAt(5, 1, Item::get(Item::CONCRETE, 5)->setCustomName("§l§aKLIKNIJ ABY ULEPSZYC ITEM"));
            $this->setItemAt(5, 3, Item::get(Item::CONCRETE, 5)->setCustomName("§l§aKLIKNIJ ABY ULEPSZYC ITEM"));

            return false;
        }

        if($sourceItem->getId() === Item::CONCRETE){
            if($sourceItem->getDamage() !== 5)
                return true;

            $item = $this->getItem(13);

            if(!$item instanceof Pickaxe)
                return true;

            $this->setItemAt(4, 2, Item::get(Item::BOTTLE_O_ENCHANTING)->setCustomName(" "));
            $this->setItemAt(5,2, Item::get(Item::AIR));
            $this->setItemAt(5, 1, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY MOC GO ULEPSZYC"));
            $this->setItemAt(5, 3, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY MOC GO ULEPSZYC"));

            if(InventoryUtil::hasItem($player, new UpgradeFragment())){
                $item->addEnchantment(new EnchantmentInstance(Enchantment::getEnchantment(Enchantment::EFFICIENCY), 6));
                $this->player->getLevel()->broadcastLevelSoundEvent($this->player, LevelSoundEventPacket::SOUND_BLAST);
                ParticlesManager::spawnFirework($this->player, $this->player->getLevel(), [[ParticlesManager::TYPE_HUGE_SPHERE, ParticlesManager::COLOR_DARK_PURPLE], [ParticlesManager::TYPE_HUGE_SPHERE, ParticlesManager::COLOR_BLUE]]);
                $player->getInventory()->removeItem(new UpgradeFragment());
                $player->getInventory()->addItem($item);
            }else {
                UpgradeManager::openUpgradeDraw($player, $item);
                LogManager::sendLog($player, "Upgrade", LogManager::BLACK_SMITH);
            }

            PacketManager::unClickButton($player);
        }

        return true;
    }

    public function onClose(Player $who) : void {

        $item = $this->getItem(13);

        if($item->getId() !== ItemIds::AIR)
            $who->getInventory()->addItem($item);

        parent::onClose($who);
    }
}