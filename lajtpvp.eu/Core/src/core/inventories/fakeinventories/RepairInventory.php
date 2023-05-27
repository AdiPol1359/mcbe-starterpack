<?php

namespace core\inventories\fakeinventories;

use core\inventories\FakeInventory;
use core\utils\InventoryUtil;
use core\utils\MessageUtil;
use core\utils\SoundUtil;
use pocketmine\block\Air;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\TieredTool;
use pocketmine\item\Tool;
use pocketmine\item\ToolTier;
use pocketmine\player\Player;

class RepairInventory extends FakeInventory {

    private array $tiers;

    private int $selectedTier = 5;
    private float $cost = 0.0;

    public function __construct() {
        $this->tiers = [
            ToolTier::GOLD()->id() => ["item" => ItemIds::GOLD_INGOT, "name" => "Zloto"],
            ToolTier::WOOD()->id() => ["item" => ItemIds::WOOD, "name" => "Drewno"],
            ToolTier::STONE()->id() => ["item" => ItemIds::COBBLESTONE, "name" => "Cobblestone"],
            ToolTier::IRON()->id() => ["item" => ItemIds::IRON_INGOT, "name" => "Zelazo"],
            ToolTier::DIAMOND()->id() => ["item" => ItemIds::DIAMOND, "name" => "Diamenty"]
        ];
        
        parent::__construct("§eKOWADLO");
    }

    public function setItems() : void {
        $itemFactory = ItemFactory::getInstance();
        $this->fill();

        $this->setItemAt(4, 2, $itemFactory->get(ItemIds::BOTTLE_O_ENCHANTING)->setCustomName(" "));
        $this->setItemAt(5, 2, $itemFactory->get(ItemIds::AIR));
        $this->setItemAt(5, 1, $itemFactory->get(ItemIds::CONCRETE, 14)->setCustomName("§cWSTAW ITEM W POLE ABY MOC GO NAPRAWIC"));
        $this->setItemAt(5, 3, $itemFactory->get(ItemIds::CONCRETE, 14)->setCustomName("§cWSTAW ITEM W POLE ABY MOC GO NAPRAWIC"));
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {
        $itemFactory = ItemFactory::getInstance();

        if($sourceItem->getId() !== ItemIds::STAINED_GLASS_PANE)
            SoundUtil::addSound([$player], $this->holder, "random.click");

        if($slot === 13) {
            $tier = ToolTier::DIAMOND();

            if($targetItem instanceof TieredTool)
                $tier = $targetItem->getTier();

            if($targetItem instanceof Armor) {
                $iron = [309, 308, 307, 306];
                $gold = [317, 316, 315, 314];

                if(in_array($targetItem->getId(), $iron))
                    $tier = ToolTier::IRON();

                if(in_array($targetItem->getId(), $gold))
                    $tier = ToolTier::GOLD();
            }

            $this->selectedTier = $tier;
        }

        if($slot === 13) {

            if($sourceItem instanceof Tool || $sourceItem instanceof Armor) {
                $this->setItemAt(4, 2, $itemFactory->get(ItemIds::BOTTLE_O_ENCHANTING)->setCustomName(" "));
                $this->setItemAt(5, 1, $itemFactory->get(ItemIds::CONCRETE, 14)->setCustomName("§cWSTAW ITEM W POLE ABY MOC GO NAPRAWIC"));
                $this->setItemAt(5, 3, $itemFactory->get(ItemIds::CONCRETE, 14)->setCustomName("§cWSTAW ITEM W POLE ABY MOC GO NAPRAWIC"));
                $this->cost = 0;
                return false;
            }

            if(!$targetItem instanceof Tool && !$targetItem instanceof Armor && !$targetItem instanceof Air)
                return true;

            if($targetItem->getDamage() <= 0) {
                $this->setItemAt(5, 1, $itemFactory->get(ItemIds::CONCRETE, 14)->setCustomName("§cTEN PRZEDMIOT NIE JEST USZKODZONY"));
                $this->setItemAt(5, 3, $itemFactory->get(ItemIds::CONCRETE, 14)->setCustomName("§cTEN PRZEDMIOT NIE JEST USZKODZONY"));
                return false;
            }

            if($targetItem->getId() !== ItemIds::AIR && $targetItem instanceof Armor || $targetItem instanceof Tool)
                $this->cost += ($targetItem->getDamage() / 100);

            $this->cost += $targetItem->getBlockToolHarvestLevel() / 2;
            foreach($targetItem->getEnchantments() as $enchantment)
                $this->cost += ($enchantment->getLevel() / 3);

            $this->cost = ceil((float) number_format($this->cost, 2, '.', ''));

            $this->setItemAt(5, 1, $itemFactory->get(ItemIds::CONCRETE, 5)->setCustomName("§aKLIKNIJ ABY NAPRAWIC ITEM"));
            $this->setItemAt(5, 3, $itemFactory->get(ItemIds::CONCRETE, 5)->setCustomName("§aKLIKNIJ ABY NAPRAWIC ITEM"));
            $this->setItemAt(4, 2, $itemFactory->get(ItemIds::BOTTLE_O_ENCHANTING)->setCustomName("§eINFORMACJE O NAPRAWIE" . "\n\n" .
                "§8» §r§7Wymagany poziom: §e" . $this->cost ."\n"."§r§8» §r§7Cena: §e".$this->getTier($this->selectedTier)["name"]." x".ceil($this->getCost($targetItem))));

            return false;
        }

        if($sourceItem->getId() === ItemIds::CONCRETE) {
            if($sourceItem->getMeta() !== 5)
                return true;

            $item = $this->getItem(13);
            $cost = ceil($this->cost);

            if(!$item instanceof Armor && !$item instanceof Tool) {
                $this->unClickItem($player);
                return true;
            }

            if($player->getXpManager()->getXpLevel() < $cost) {
                $this->closeFor($player);
                $player->sendMessage(MessageUtil::format("Nie maszy wystarczajaco duzo pieniedzy aby naprawic ten przedmiot! Brakuje ci §e" . abs($player->getXpManager()->getXpLevel() - round($this->cost)) . "§r§7 levela"));
                return true;
            }

            $containItem = $itemFactory->get($this->getTier($this->selectedTier)["item"], 0, ceil($this->getCost($item)));

            if(!$player->getInventory()->contains($containItem)) {
                $this->closeFor($player);
                $player->sendMessage(MessageUtil::format("Nie maszy wystarczajaco duzo surowcow aby naprawic ten przedmiot!"));
                return true;
            }

            $player->getInventory()->removeItem($containItem);
            $player->getXpManager()->setXpLevel($player->getXpManager()->getXpLevel() - $cost);

            $this->setItemAt(4, 2, $itemFactory->get(ItemIds::BOTTLE_O_ENCHANTING)->setCustomName(" "));
            $this->setItemAt(5, 2, $itemFactory->get(ItemIds::AIR));
            $this->setItemAt(5, 1, $itemFactory->get(ItemIds::CONCRETE, 14)->setCustomName("§cWSTAW ITEM W POLE ABY MOC GO NAPRAWIC"));
            $this->setItemAt(5, 3, $itemFactory->get(ItemIds::CONCRETE, 14)->setCustomName("§cWSTAW ITEM W POLE ABY MOC GO NAPRAWIC"));
            $item->setDamage(0);

            InventoryUtil::addItem($item, $player);
        }

        $this->unClickItem($player);
        return true;
    }

    private function getCost(Item $item) : int{
        if ($item instanceof Tool || $item instanceof Armor) {
            if ($item->getDamage() / $item->getMaxDurability() * 100 <= 25) {
                return 1;
            } elseif ($item->getDamage() / $item->getMaxDurability() * 100 <= 50) {
                return 2;
            } elseif ($item->getDamage() / $item->getMaxDurability() * 100 <= 75) {
                return 3;
            } elseif ($item->getDamage() / $item->getMaxDurability() * 100 <= 100) {
                return 4;
            } else {
                return 0;
            }
        }

        return 0;
    }

    private function getTier(int $tier) : array {
        return $this->tiers[$tier] ?? $this->tiers[ToolTier::DIAMOND()->id()];
    }

    public function onClose(Player $who) : void {

        $item = $this->getItem(13);

        if($item->getId() !== ItemIds::AIR)
            InventoryUtil::addItem($item, $who);

        parent::onClose($who);
    }
}