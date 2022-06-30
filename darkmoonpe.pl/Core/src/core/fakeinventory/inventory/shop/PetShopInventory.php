<?php

namespace core\fakeinventory\inventory\shop;

use core\fakeinventory\FakeInventory;
use core\manager\managers\item\LoreCreator;
use core\manager\managers\LogManager;
use core\manager\managers\PacketManager;
use core\manager\managers\ParticlesManager;
use core\manager\managers\pet\PetManager;
use core\manager\managers\SoundManager;
use core\user\UserManager;
use core\util\utils\ItemUtil;
use core\util\utils\MessageUtil;
use pocketmine\item\Item;
use pocketmine\Player;

class PetShopInventory extends FakeInventory {

    public function __construct(Player $player) {
        parent::__construct($player, "§l§9SKLEP Z ZWIERZAKAMI", self::BIG);

        $this->setItems();
    }

    public function setItems() : void {

        $ironBatsSlot = [0, 1, 2, 3, 4, 5, 6, 7, 8, 45, 46, 47, 48, 50, 51, 52, 53];

        foreach($ironBatsSlot as $slot)
            $this->setItem($slot, Item::get(Item::IRON_BARS)->setCustomName(" "));

        $this->setItem(49, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWYLACZ ZWIERZAKI"));

        $petSlot = 9;

        foreach(PetManager::getPets() as $name => $pet){

            $petItem = Item::get(Item::SPAWN_EGG, $pet->getNetworkID());

            $petItem->getNamedTag()->setString("petItem", $pet->getName());

            $this->setItem($petSlot, $this->correctPet($petItem, strtoupper($pet->getDisplayName()), $pet->getName(), "§9"));
            $petSlot++;
        }
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== Item::IRON_BARS)
            SoundManager::addSound($player, $this->holder, "random.click");

        $namedTag = $sourceItem->getNamedTag();

        if($sourceItem->getId() === Item::CONCRETE && $sourceItem->getDamage() === 14){
            UserManager::getUser($player->getName())->setSelectedPet(null);

            if(($pets = PetManager::getSpecifyPlayerPets($player->getName()))) {
                foreach(PetManager::getSpecifyPlayerPets($player->getName()) as $key => $pet)
                    $pet->getEntity()->close();
            }
        }

        if($namedTag->offsetExists("petItem")){
            $petName = $namedTag->getString("petItem");

            $pet = PetManager::getPet($petName);

            $user = UserManager::getUser($player->getName());

            $user->hasPet($pet) ? $status = true : $status = false;

            if(!$status) {
                if(($playerMoney = $user->getPlayerMoney()) < $pet->getPrice()) {
                    $this->closeFor($player);
                    $player->sendMessage(MessageUtil::format("Nie masz wystarczajaco duzo pieniedzy! Brakuje ci §l§9" . abs($playerMoney - $pet->getPrice()) . "§r§7zl Aby kupic tego zwierzaka"));
                    return true;
                }

                $user->reducePlayerMoney($pet->getPrice());
                $user->addPet($pet);

                ParticlesManager::spawnFirework($player, $player->getLevel(), [[ParticlesManager::TYPE_HUGE_SPHERE, ParticlesManager::COLOR_DARK_PURPLE], [ParticlesManager::TYPE_HUGE_SPHERE, ParticlesManager::COLOR_BLUE]]);

                LogManager::sendLog($player, "BuyPet: ".$pet->getName()." [".$pet->getPrice()."zl]", LogManager::SHOP);
                $this->setItems();
            }

            if(($pets = PetManager::getSpecifyPlayerPets($player->getName())) !== null) {
                foreach($pets as $playerPet)
                    $playerPet->getEntity()->close();
            }

            if(!$user->hasPet($pet))
                $user->addPet($pet);

            PetManager::spawnPet($pet, $player, "§7Zwierzak gracza: §l§9".$player->getName());
            $user->setSelectedPet($pet);
        }

        PacketManager::unClickButton($player);
        return true;
    }

    private function correctPet(Item $item, string $lorePetName, string $petName, string $color) : Item {

        $user = UserManager::getUser($this->player->getName());
        $pet = PetManager::getPet($petName);

        $status = "§r§7» §l§cNIE KUPIONE §r§7«";
        $clickFor = "§r§8(Nacisnij aby kupic)";

        if($user->hasPet($pet)) {
            ItemUtil::addItemGlow($item);
            $status = "§r§7» §l§aPOSIADANE §r§7«";
            $clickFor = "§r§8(Nacisnij aby zrespic)";
        }

        $item->setCustomName("§r§7[§8---===§7[ §r§l".$color.$lorePetName."§r§7 ]§8===---§7]");

        $loreCreator = new LoreCreator();
        $loreCreator->setCustomName($item->getCustomName(), true);
        $loreCreator->setLore([
            "",
            "§r§7Koszt §9§l".$pet->getPrice()."§r§8zl§7!",
            $status,
            "  ".$clickFor,
            "  §r§7Zwierzaki sa na zawsze",
            "  §r§7Kazdy widzi zwierzaki",
            "  §r§7/ustawienia aby je ukryc",
            ""
        ], true);

        $loreCreator->alignCustomName(64);
        $loreCreator->alignLore();

        $item->setCustomName($loreCreator->getCustomName());
        $item->setLore($loreCreator->getLore());

        return $item;
    }
}