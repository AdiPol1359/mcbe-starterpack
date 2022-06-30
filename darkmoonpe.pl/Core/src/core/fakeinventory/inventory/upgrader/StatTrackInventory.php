<?php

namespace core\fakeinventory\inventory\upgrader;

use core\fakeinventory\FakeInventory;
use core\manager\managers\PacketManager;
use core\manager\managers\ParticlesManager;
use core\manager\managers\SoundManager;
use core\manager\managers\StatTrackManager;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\block\Air;
use pocketmine\item\Axe;
use pocketmine\item\Hoe;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\Pickaxe;
use pocketmine\item\Shovel;
use pocketmine\Player;

class StatTrackInventory extends FakeInventory {

    public function __construct(Player $player) {

        parent::__construct($player, "§l§9STATTRACK", self::SMALL);
        $this->setItems();
    }

    public function setItems() : void{
        $this->fillBars();

        $this->setItemAt(5,2, Item::get(Item::AIR));
        $this->setItemAt(5, 1, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY ZARZADZAC STATTRACKIEM"));
        $this->setItemAt(5, 3, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY ZARZADZAC STATTRACKIEM"));
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== Item::IRON_BARS)
            SoundManager::addSound($player, $this->holder, "random.click");

        if($slot === 13){

            if($this->instanceofTool($sourceItem)){
                $this->setItemAt(3, 2, Item::get(Item::IRON_BARS));
                $this->setItemAt(7, 2, Item::get(Item::IRON_BARS));

                $this->setItemAt(5, 1, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY ZARZADZAC STATTRACKIEM"));
                $this->setItemAt(5, 3, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY ZARZADZAC STATTRACKIEM"));
                return false;
            }

            if(!$this->instanceofTool($targetItem) && !$targetItem instanceof Air)
                return true;

            if(!StatTrackManager::hasStatTrack($targetItem))
                $this->setItemAt(3, 2, Item::get(Item::DYE, 10)->setCustomName("§aKup StatTrack za §l100zl"));
            else
                $this->setItemAt(7, 2, Item::get(Item::DYE, 1)->setCustomName("§cResetuj StatTrack za §l5zl"));

            $this->setItemAt(5, 1, Item::get(Item::CONCRETE, 5)->setCustomName("§l§aWYBIERZ INTERESUJACA CIE OPCJE"));
            $this->setItemAt(5, 3, Item::get(Item::CONCRETE, 5)->setCustomName("§l§aWYBIERZ INTERESUJACA CIE OPCJE"));

            return false;
        }

        if($sourceItem->getId() === Item::DYE){
            if($sourceItem->getDamage() === 10) {

                $item = $this->getItem(13);
                $cost = ConfigUtil::STATTRACK_BUY_COST;

                if(!$this->instanceofTool($item))
                    return true;

                $user = UserManager::getUser($player->getName());

                if($user->getPlayerMoney() < $cost) {
                    $this->closeFor($player);
                    $player->sendMessage(MessageUtil::format("Nie maszy wystarczajaco duzo pieniedzy aby nadac stattrack na ten przedmiot! Brakuje ci §l§9".abs($user->getPlayerMoney() - $cost)."§r§7zl"));
                    return true;
                }

                $user->reducePlayerMoney($cost);

                $this->setItemAt(5, 2, Item::get(Item::AIR));
                $this->setItemAt(5, 1, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY ZARZADZAC STATTRACKIEM"));
                $this->setItemAt(5, 3, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY ZARZADZAC STATTRACKIEM"));
                $this->setItemAt(3, 2, Item::get(Item::IRON_BARS));
                $this->setItemAt(7, 2, Item::get(Item::IRON_BARS));

                StatTrackManager::addStatTrack($item);
                $player->getInventory()->addItem($item);
                ParticlesManager::spawnFirework($this->player, $this->player->getLevel(), [[ParticlesManager::TYPE_HUGE_SPHERE, ParticlesManager::COLOR_DARK_PURPLE], [ParticlesManager::TYPE_HUGE_SPHERE, ParticlesManager::COLOR_BLUE]]);
                PacketManager::unClickButton($player);
            }

            if($sourceItem->getDamage() === 1){

                $item = $this->getItem(13);
                $cost = ConfigUtil::STATTRACK_RESET_COST;

                if(!$this->instanceofTool($item))
                    return true;

                $user = UserManager::getUser($player->getName());

                if($user->getPlayerMoney() < $cost) {
                    $this->closeFor($player);
                    $player->sendMessage(MessageUtil::format("Nie maszy wystarczajaco duzo pieniedzy aby zresetowac stattrack na tym przedmiocie! Brakuje ci §l§9".abs($user->getPlayerMoney() - $cost)."§r§7zl"));
                    return true;
                }

                $user->reducePlayerMoney($cost);

                $this->setItemAt(5, 2, Item::get(Item::AIR));
                $this->setItemAt(5, 1, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY ZARZADZAC STATTRACKIEM"));
                $this->setItemAt(5, 3, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWSTAW ITEM W POLE ABY ZARZADZAC STATTRACKIEM"));
                $this->setItemAt(3, 2, Item::get(Item::IRON_BARS));
                $this->setItemAt(7, 2, Item::get(Item::IRON_BARS));

                StatTrackManager::resetStatTrack($item);
                $player->getInventory()->addItem($item);
            }
        }

        PacketManager::unClickButton($player);
        return true;
    }

    public function onClose(Player $who) : void {

        $item = $this->getItem(13);

        if($item->getId() !== ItemIds::AIR)
            $who->getInventory()->addItem($item);

        parent::onClose($who);
    }

    public function instanceofTool(Item $item) : bool{
        return $item instanceof Pickaxe || $item instanceof Axe || $item instanceof Hoe || $item instanceof Shovel;
    }
}