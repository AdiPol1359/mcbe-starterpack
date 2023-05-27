<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories\guild\war;

use core\inventories\FakeInventory;
use core\inventories\FakeInventoryPatterns;
use core\utils\LoreCreator;
use core\managers\war\War;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class WarInventory extends FakeInventory {

    private ?War $war;
    public function __construct(War $war) {
        $this->war = $war;
        parent::__construct("§l§eWOJNA");
    }

    public function setItems() : void {
        $itemFactory = ItemFactory::getInstance();
        
        $this->fillWithPattern(FakeInventoryPatterns::PATTERN_FILL_CORNERS_SMALL);

        if(!$this->war)
            return;

        $book = $itemFactory->get(ItemIds::BOOK);
        $book->setCustomName("§7[§8---===§7[ §e".$this->war->getAttacker()."§7 ]§8===---§7]");

        $loreCreator = new LoreCreator();
        $loreCreator->setCustomName($book->getCustomName(), true);
        $loreCreator->setLore([
            "",
            "§r§7Zabojstwa §e".$this->war->getAttackerStat(War::STAT_KILLS),
            "§r§7Smierci §e".$this->war->getAttackerStat(War::STAT_DEATHS),
            ""
        ], true);

        $loreCreator->alignLore();
        $book->setCustomName($loreCreator->getCustomName());
        $book->setLore($loreCreator->getLore());

        $this->setItem(12, $book, true, true);

        $attackedBook = $itemFactory->get(ItemIds::BOOK);
        $attackedBook->setCustomName("§7[§8---===§7[ §e".$this->war->getAttacked()."§7 ]§8===---§7]");

        $loreCreator = new LoreCreator();
        $loreCreator->setCustomName($attackedBook->getCustomName(), true);
        $loreCreator->setLore([
            "",
            "§r§7Zabojstwa §e".$this->war->getAttackedStat(War::STAT_KILLS),
            "§r§7Smierci §e".$this->war->getAttackedStat(War::STAT_DEATHS),
            ""
        ], true);

        $loreCreator->alignLore();
        $attackedBook->setCustomName($loreCreator->getCustomName());
        $attackedBook->setLore($loreCreator->getLore());

        $this->setItem(14, $attackedBook, true, true);

        $clock = $itemFactory->get(ItemIds::CLOCK);
        $clock->setCustomName("§7[§8---===§7[ §eINFORMACJE§7 ]§8===---§7]");

        $loreCreator = new LoreCreator();
        $loreCreator->setCustomName($clock->getCustomName(), true);
        $loreCreator->setLore([
            "",
            "§r§7Rozpoczecie §e".(date("d.m.Y H:i", $this->war->getStartTime())),
            "§r§7Koniec §e".(date("d.m.Y H:i", $this->war->getEndTime())),
            "§r§7Aktualnie wygrywa §e".$this->war->getActualWinner(),
            ""
        ], true);

        $loreCreator->alignLore();
        $clock->setCustomName($loreCreator->getCustomName());
        $clock->setLore($loreCreator->getLore());

        $this->setItem(13, $clock, true, true);

    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {
        $this->unClickItem($player);
        return true;
    }
}