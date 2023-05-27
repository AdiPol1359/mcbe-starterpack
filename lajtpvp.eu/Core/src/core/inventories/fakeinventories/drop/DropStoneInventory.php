<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories\drop;

use core\inventories\FakeInventory;
use core\inventories\FakeInventorySize;
use core\Main;
use core\managers\drop\Drop;
use core\utils\PermissionUtil;
use core\utils\Settings;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class DropStoneInventory extends FakeInventory {

    public function __construct(private Player $player) {
        parent::__construct("§l§eDROP Z KAMIENIA", FakeInventorySize::LARGE_CHEST);
    }

    public function setItems() : void{
        $this->fill();

        $user = Main::getInstance()->getUserManager()->getUser($this->player->getName());

        if(!$user)
            return;

        foreach(Main::getInstance()->getDropManager()->getDrop() as $drop) {
            $item = clone $drop->getDrop()["what"];
            $item->setCustomName(" ");
            $this->checkDropStatus($item, $drop);
            $this->setItem($drop->getSlot(), $item);
        }

        $disableAll = ItemFactory::getInstance()->get(ItemIds::CONCRETE, 14)->setCustomName("§r§l§cWYLACZ WSZYSTKO");
        $enableAll = ItemFactory::getInstance()->get(ItemIds::CONCRETE, 5)->setCustomName("§r§l§aWLACZ WSZYSTKO");

        $pickaxe = ItemFactory::getInstance()->get(ItemIds::DIAMOND_PICKAXE)->setCustomName("§r§7PARTICLESY Z KAMIENIA §l");

//        if(Main::getInstance()->getUserManager()->getUser($this->player->getName())->isDropEnabled("particle")){
//            ItemUtil::addItemGlow($pickaxe);
//            $pickaxe->setCustomName($pickaxe->getCustomName()."§l§aWLACZONE");
//        }else
//            $pickaxe->setCustomName($pickaxe->getCustomName()."§l§cWYLACZONE");

        $backButton = VanillaItems::NETHER_STAR()->setCustomName("§r§l§cPOWROT");

        $this->setItemAt(5, 5, $backButton);
        $this->setItemAt(2, 5, $enableAll);
        $this->setItemAt(3, 5, $disableAll);
        $this->setItemAt(8, 5, $pickaxe);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        $user = Main::getInstance()->getUserManager()->getUser($player->getName());
        $namedTag = $sourceItem->getNamedTag();

        if(($dropId = $namedTag->getInt("dropId", -1)) !== -1) {
            $user->getDropManager()->switchDrop(Main::getInstance()->getDropManager()->getDropById($dropId));
            $this->setItems();
        }

        switch($sourceItem->getId()){
            case ItemIds::CONCRETE:

                //$particle = $user->isDropEnabled("particle");

                if($sourceItem->getMeta() === 14)
                    $user->getDropManager()->setAllDrops(false);

                if($sourceItem->getMeta() === 5)
                    $user->getDropManager()->setAllDrops(true);

                //$user->setDrop("particle", $particle);

                $this->setItems();
                break;

            case ItemIds::DIAMOND_PICKAXE:

                //$user->switchDrop("particle");
                $this->setItems();
                break;

            case ItemIds::NETHER_STAR:
                $this->changeInventory($player, new MainDropInventory());
                break;

        }

        $this->unClickItem($player);
        return true;
    }

    private function correctText(string $text, int $length = 65) : string{
        $lines = ($length - strlen($text));
        $resultString = $text;

        if($lines > 0){
            if($lines % 2 === 0 || $lines > 2)
                $resultString = str_replace("---", str_repeat("-", intval(round(($lines / 2) + 3))), $resultString);
        }

        return $resultString;
    }

    private function checkDropStatus(Item $item, Drop $drop) : void {
        $dropManager = Main::getInstance()->getUserManager()->getUser($this->player->getName())->getDropManager();

        $bonus = 0;

        foreach($drop->getBonuses() as $permission => $bonus) {
            if(PermissionUtil::has($this->player, Settings::$PERMISSION_TAG . $bonus)) {
                $bonus += $bonus;
            }
        }

        $item->setCustomName(" ");

        $item->setCustomName("§r§7[§8---===§7[ §r§l§e".strtoupper($drop->getName())."§r§7 ]§8===---§7]");
        $item->setLore([
            "§r",
            " §r§7» Szanse: §e".$drop->getChance()." §7+ §8(§7".$bonus."§8)§7%",
            " §r§7» Status: ".($dropManager->isDropEnabled($drop) ? "§aWLACZONY" : "§cWYLACZONY"),
            " §r§7» Fortuna: ".($drop->isFortune() ? "§aTAK" : "§cNIE"),
            " §r§7» Turbodrop: ".($drop->isTurbo() ? "§aTAK" : "§cNIE")
        ]);

        if($dropManager->isDropEnabled($drop)) {
            $item->addEnchantment(new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId(-1)));
        }

        $item->getNamedTag()->setInt("dropId", $drop->getDropId());

    }
}