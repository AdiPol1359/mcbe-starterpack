<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories\guild\war;

use core\inventories\FakeInventory;
use core\inventories\FakeInventoryPatterns;
use core\Main;
use core\utils\CustomItemUtil;
use core\utils\LoreCreator;
use core\managers\war\War;
use core\utils\SoundUtil;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class WarsInventory extends FakeInventory {

    private int $page;
    private array $wars = [];

    public function __construct() {
        $this->page = 1;

        parent::__construct("§eWOJNY");
    }

    public function onOpen(Player $who) : void {
        $this->page = 1;
        $this->wars[$this->page] = [];
        parent::onOpen($who);
    }

    public function setItems() : void {
        $itemFactory = ItemFactory::getInstance();
        
        $this->clearAll();
        $this->fillWithPattern(FakeInventoryPatterns::PATTERN_FILL_CORNERS_SMALL);
        $this->updateWars();

        if(!isset($this->wars[$this->page]))
            $this->page = max(1, $this->page - 1);

        $lastSlot = 10;

        if(isset($this->wars[$this->page])) {
            foreach ($this->wars[$this->page] as $war) {
                $this->setWar(Main::getInstance()->getWarManager()->getWarById($war->getId()), $lastSlot);
                $lastSlot++;
            }
        }

        $pageBack = new CustomItemUtil(ItemIds::CONCRETE);
        $pageBack->setCustomName("POPRZEDNIA STRONA");
        if($this->page <= 1) {
            $pageBack->setMeta(14);
            $pageBack->setCustomName("§c".$pageBack->getCustomName());
        } else {
            $pageBack->setCustomName("§a".$pageBack->getCustomName());
            $pageBack->setMeta(5);
        }

        $pageNext = new CustomItemUtil(ItemIds::CONCRETE);
        $pageNext->setCustomName("NASTEPNA STRONA");
        if(max(array_keys($this->wars)) > $this->page) {
            $pageNext->setMeta(5);
            $pageNext->setCustomName("§a".$pageNext->getCustomName());
        }else {
            $pageNext->setCustomName("§c".$pageNext->getCustomName());
            $pageNext->setMeta(14);
        }

        $bottle = $itemFactory->get(ItemIds::BOTTLE_O_ENCHANTING);
        $bottle->setCustomName("§7[§8---===§7[ §eINFORMACJE§7 ]§8===---§7]");

        $loreCreator = new LoreCreator($bottle->getCustomName(), [
            "",
            "§r§7Laczna ilosc wojen§8: §e".count(Main::getInstance()->getWarManager()->getWars()),
            "§r§7Zakonczone wojny§8: §e".count(Main::getInstance()->getWarManager()->getEndedWars()),
            "§r§7Trwajace wojny§8: §e".(count(Main::getInstance()->getWarManager()->getWars()) - count(Main::getInstance()->getWarManager()->getEndedWars())),
            "",
        ]);

        $loreCreator->alignLore();
        $bottle->setLore($loreCreator->getLore());

        $this->setItem(21, $pageBack->getItem(), true, true);
        $this->setItem(22, $bottle, true, true);
        $this->setItem(23, $pageNext->getItem(), true, true);
    }

    public function updateWars() : void {
        $itemFactory = ItemFactory::getInstance();

        $wars = [
            1 => []
        ];

        $page = 1;

        $booksSlot = range(10, 16);

        foreach($booksSlot as $bookSlot)
            $this->setItem($bookSlot, $itemFactory->get(ItemIds::BOOK)->setCustomName(" "));

        $warsData = Main::getInstance()->getWarManager()->getWars();

        usort($warsData,function($first,$second){
            return $first->getId() < $second->getId();
        });

        foreach($warsData as $warData) {
            if(count($wars[$page]) >= 7)
                $page++;

            if(!isset($wars[$page]))
                $wars[$page] = [];

            $wars[$page][] = $warData;
        }

        $this->wars = $wars;
    }

    public function setWar(War $war, int $slot) : void {
        $itemFactory = ItemFactory::getInstance();

        $item = $itemFactory->get(ItemIds::ENCHANTED_BOOK);
        $item->setCustomName("§7[§8----====§7[ §eWojna (#".$war->getId().")§r§7 ]§8====----§7]");

        $item->setLore([
            "",
            "§r§7Koniec§8:  §e".date("d.m.Y H:i:s", $war->getEndTime()),
            "§r§7Start§8:  §e".date("d.m.Y H:i:s", $war->getStartTime()),
            "§r§7Aktualnie wygrywa§8:  §e".($war->hasEnded() ? $war->getWinner() : $war->getActualWinner()),
            "    §r§7Status§8:  ".($war->hasEnded() ? "§cZAKONCZONA" : ($war->getStartTime() <= time() ? "§aTRWA" : "§eNIE ROZPOCZETA")),
            "§r§7K§8/§7D Atakowanych§8:  §e".$war->getAttackedStat(War::STAT_KILLS)."§8/§e".$war->getAttackedStat(War::STAT_DEATHS),
            "§r§7K§8/§7D Atakujacych§8:  §e".$war->getAttackerStat(War::STAT_KILLS)."§8/§e".$war->getAttackerStat(War::STAT_DEATHS),
            "  §r§7Atakowany§8:  §e".$war->getAttacked(),
            "  §r§7Atakujacy§8: §e".$war->getAttacker(),
            ""
        ]);

        $loreCreator = new LoreCreator($item->getCustomName(), $item->getLore());
        $loreCreator->alignLore();
        $item->setLore($loreCreator->getLore());

        $item->getNamedTag()->setInt("warId", $war->getId());

        $this->setItem($slot, $item, true, true);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== ItemIds::STAINED_GLASS_PANE)
            SoundUtil::addSound([$player], $this->holder, "random.click");

        if($sourceItem->getId() === ItemIds::CONCRETE) {
            switch($slot) {
                case 21:
                    $this->page--;
                    break;

                case 23:
                    $this->page++;
                    break;
            }
            $this->setItems();
        }

        $this->unClickItem($player);
        return true;
    }
}