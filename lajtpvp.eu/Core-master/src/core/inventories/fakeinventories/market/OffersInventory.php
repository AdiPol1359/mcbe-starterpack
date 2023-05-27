<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories\market;

use core\inventories\FakeInventoryPatterns;
use core\Main;
use core\utils\CustomItemUtil;
use core\utils\LoreCreator;
use core\utils\SoundUtil;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class OffersInventory extends MarketInventory {

    private int $page;
    private array $offers = [];

    public function __construct(private Player $player) {
        $this->page = 1;
        $this->offers[$this->page] = [];
        parent::__construct( "§l§eBAZAR");
    }

    public function setItems() : void {
        $this->clearAll();

        $user = Main::getInstance()->getUserManager()->getUser($this->player->getName());

        if(!$user)
            return;

        $itemFactory = ItemFactory::getInstance();
        $bankManager = $user->getBankManager();

        $this->fillWithPattern(FakeInventoryPatterns::PATTERN_FILL_UP_AND_DOWN);
        $this->updateOffers();

        if(!isset($this->offers[$this->page]))
            $this->page = max(1, $this->page - 1);

        $lastSlot = 9;
        if(isset($this->offers[$this->page])) {
            foreach ($this->offers[$this->page] as $offer) {
                $this->setItem($lastSlot, $offer->getGUIItem());
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
        if(max(array_keys($this->offers)) > $this->page) {
            $pageNext->setMeta(5);
            $pageNext->setCustomName("§a".$pageNext->getCustomName());
        }else {
            $pageNext->setCustomName("§c".$pageNext->getCustomName());
            $pageNext->setMeta(14);
        }

        $clock = $itemFactory->get(ItemIds::CLOCK);
        $clock->setCustomName("§l§e/BAZAR");

        $gold = $itemFactory->get(ItemIds::GOLD_INGOT);
        $gold->setCustomName("§7[§8----====§7[ §eBANK BAZARU§r§7 ]§8====----§7]");

        $loreCreator = new LoreCreator();
        $loreCreator->setCustomName($gold->getCustomName(), true);
        $loreCreator->setLore([
            "",
            "§r§7Posiadane zloto §e".$bankManager->getBankStatus("gold"),
            "§r§8(§7Kliknij aby wyplacac w stakach§8)",
            ""
        ], true);

        $loreCreator->alignLore();
        $gold->setCustomName($loreCreator->getCustomName());
        $gold->setLore($loreCreator->getLore());

        $this->setItemAt(4, 6, $pageBack->getItem());
        $this->setItemAt(5, 1, $gold);
        $this->setItemAt(5, 6, $clock);
        $this->setItemAt(6, 6, $pageNext->getItem());
    }

    public function updateOffers() : void {
        $offers = [
            1 => []
        ];
        $page = 1;

        foreach(Main::getInstance()->getMarketManager()->getOffers() as $offer) {
            if(count($offers[$page]) >= 36)
                $page++;

            if(!isset($offers[$page]))
                $offers[$page] = [];

            $offers[$page][] = $offer;
        }

        $this->offers = $offers;
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {
        if($sourceItem->getId() !== ItemIds::STAINED_GLASS_PANE)
            SoundUtil::addSound([$player], $this->holder, "random.click");

        if($sourceItem->getId() === ItemIds::CONCRETE) {
            switch($slot) {
                case 48:
                    $this->page--;
                    break;

                case 50:
                    $this->page++;
                    break;
            }
            $this->setItems();
        }

        if($sourceItem->getId() === ItemIds::GOLD_INGOT) {
            $user = Main::getInstance()->getUserManager()->getUser($player->getName());
            $bankManager = $user->getBankManager();
            $itemFactory = ItemFactory::getInstance();

            if($user) {
                $gold = $bankManager->getBankStatus("gold");
                if($gold > 0) {
                    if($gold >= 64) {
                        $player->getInventory()->addItem($itemFactory->get($sourceItem->getId(), $sourceItem->getMeta(), 64));
                        $bankManager->reduceBankStatus("gold", 64);
                    } else {
                        $player->getInventory()->addItem($itemFactory->get($sourceItem->getId(), $sourceItem->getMeta()));
                        $bankManager->reduceBankStatus("gold");
                    }

                    $this->setItems();
                }
            }
        }

        foreach($this->offers[$this->page] as $offer) {
            if($offer->getGUIItem()->equalsExact($sourceItem)) {
                Main::getInstance()->getMarketManager()->handleClickOffer($player, $offer);
                $this->unClickItem($player);
                return true;
            }
        }

        $this->unClickItem($player);
        return true;
    }
}