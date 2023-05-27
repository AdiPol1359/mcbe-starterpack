<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories;

use core\inventories\FakeInventory;
use core\inventories\FakeInventoryManager;
use core\inventories\FakeInventorySize;
use core\Main;
use core\managers\safe\Safe;
use core\utils\Settings;
use core\utils\SoundUtil;
use pocketmine\item\Item;

use core\items\custom\Safe as SafeM;
use pocketmine\player\Player;

class SafeInventory extends FakeInventory {

    public function __construct(private Safe $safe) {
        parent::__construct("§l§eSEJF §8(§r§7#§e".$safe->getSafeId()."§l§8)", FakeInventorySize::LARGE_CHEST);
    }

    public function setItems() : void {
        foreach($this->safe->getItems() as $slot => $item)
            $this->setItem($slot, $item, true, false);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {
        if(Main::getInstance()->getSafeManager()->isSafe($targetItem) || Main::getInstance()->getSafeManager()->isSafe($sourceItem))
            return true;

        if(($clickedItem = $this->safe->getItemFromSAfe($slot))) {
            if($sourceItem->getId() !== $targetItem->getId()) {
                if(!$clickedItem->equals($sourceItem))
                    return true;

                $this->safe->removeItemFromSafe($slot);

                foreach(FakeInventoryManager::getInventories() as $playerInv => $inventory) {
                    if($inventory instanceof SafeInventory)
                        $inventory->setItems();
                }
            }

            $this->safe->setItem($slot, $targetItem);
        }

        foreach(FakeInventoryManager::getInventories() as $playerInv => $inventory) {
            if($inventory instanceof SafeInventory)
                $inventory->setItems();
        }

        return false;
    }

    public function onClose(Player $who) : void {

        SoundUtil::addSound([$who], $who->getPosition(), "random.shulkerboxclosed");

        $itemInHand = $who->getInventory()->getItemInHand();
        $user = Main::getInstance()->getUserManager()->getUser($who->getName());

        $user?->setLastData(Settings::$SAFE_LAST_OPEN, (time() + Settings::$SAFE_LAST_OPEN_TIME), Settings::$TIME_TYPE);

        if(Main::getInstance()->getSafeManager()->isSafe($itemInHand)) {
            $safe = Main::getInstance()->getSafeManager()->getSafeById($itemInHand->getNamedTag()->getInt("safeId"));

            if($safe->getSafeId() === $this->safe->getSafeId())
                $who->getInventory()->setItemInHand((new SafeM($safe))->__toItem());
        }

        parent::onClose($who);
    }
}