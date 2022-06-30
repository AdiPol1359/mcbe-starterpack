<?php

namespace core\fakeinventory\inventory\shop;

use core\fakeinventory\FakeInventory;
use core\manager\managers\item\LoreCreator;
use core\manager\managers\LogManager;
use core\manager\managers\PacketManager;
use core\manager\managers\particle\ParticleManager;
use core\manager\managers\ParticlesManager;
use core\manager\managers\SoundManager;
use core\user\UserManager;
use core\util\utils\ItemUtil;
use core\util\utils\MessageUtil;
use pocketmine\item\Item;
use pocketmine\Player;

class ParticleShopInventory extends FakeInventory {

    public function __construct(Player $player) {
        parent::__construct($player, "§l§9SKLEP Z PARTICLESAMI", self::BIG);

        $this->setItems();
    }

    public function setItems() : void {

        $ironBatsSlot = [0, 1, 2, 3, 4, 5, 6, 7, 8, 45, 46, 47, 48, 50, 51, 52, 53];

        foreach($ironBatsSlot as $slot)
            $this->setItem($slot, Item::get(Item::IRON_BARS)->setCustomName(" "));

        $this->setItem(49, Item::get(Item::CONCRETE, 14)->setCustomName("§l§cWYLACZ PARTICLESY"));

        $particleSlot = 9;

        foreach(ParticleManager::getParticles() as $key => $particle){

            $particleItem = $particle->getInventoryItem();
            $particleItem->getNamedTag()->setString("particleItem", $particle->getName());

            $this->setItem($particleSlot, $this->correctParticle($particleItem, strtoupper($particle->getName()), $particle->getName(), "§9"));
            $particleSlot++;
        }
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== Item::IRON_BARS)
            SoundManager::addSound($player, $this->holder, "random.click");

        $namedTag = $sourceItem->getNamedTag();

        if($sourceItem->getId() === Item::CONCRETE && $sourceItem->getDamage() === 14){
            foreach(ParticleManager::getPlayerParticles($player->getName()) as $key => $particle) {
                $particle->removePlayer($player->getName());
                UserManager::getUser($player->getName())->setSelectedParticle();
            }
        }

        if($namedTag->offsetExists("particleItem")){
            $particleName = $namedTag->getString("particleItem");
            
            $particle = ParticleManager::getParticle($particleName);

            $user = UserManager::getUser($player->getName());

            $user->hasParticle($particle) ? $status = true : $status = false;

            if(!$status) {

                if(($playerMoney = $user->getPlayerMoney()) < $particle->getCost()) {
                    $this->closeFor($player);
                    $player->sendMessage(MessageUtil::format("Nie masz wystarczajaco duzo pieniedzy! Brakuje ci §l§9" . abs($playerMoney - $particle->getCost()) . "§r§7zl Aby kupic te particle"));
                    return true;
                }

                $user->reducePlayerMoney($particle->getCost());
                $user->addParticle($particle);

                ParticlesManager::spawnFirework($player, $player->getLevel(), [[ParticlesManager::TYPE_HUGE_SPHERE, ParticlesManager::COLOR_DARK_PURPLE], [ParticlesManager::TYPE_HUGE_SPHERE, ParticlesManager::COLOR_BLUE]]);

                LogManager::sendLog($player, "BuyParticle: ".$particle->getName()." [".$particle->getCost()."zl]", LogManager::SHOP);
                $this->setItems();
            }

            if(($particles = ParticleManager::getPlayerParticles($player->getName())) !== null) {
                foreach($particles as $playerParticle)
                    $playerParticle->removePlayer($player->getName());
            }

            if(!$particle->hasPlayer(($player->getName())))
                $particle->addPlayer($player->getName());

            $user->setSelectedParticle($particle);
        }

        PacketManager::unClickButton($player);
        return true;
    }

    private function correctParticle(Item $item, string $loreParticleName, string $particleName, string $color) : Item {

        $user = UserManager::getUser($this->player->getName());
        $particle = ParticleManager::getParticle($particleName);

        $status = "§r§7» §l§cNIE KUPIONE §r§7«";
        $clickFor = "§r§8(Nacisnij aby kupic)";

        $particle->onMove() ? $onMoveStatus = "§r§7» §l§9PODCZAS RUCHU §r§7«" : $onMoveStatus = "§r§7» §l§9PODCZAS STANIA §r§7«";

        if($user->hasParticle($particle)) {
            ItemUtil::addItemGlow($item);
            $status = "§r§7» §l§aPOSIADANE §r§7«";
            $clickFor = "§r§8(Nacisnij aby uzyc)";
        }

        $item->setCustomName("§r§7[§8---===§7[ §r§l".$color.$loreParticleName."§r§7 ]§8===---§7]");

        $loreCreator = new LoreCreator();
        $loreCreator->setCustomName($item->getCustomName(), true);
        $loreCreator->setLore([
            "",
            "§r§7Koszt §9§l".$particle->getCost()."§r§8zl§7!",
            $status,
            $clickFor,
            "§r§7Particlesy sa na zawsze",
            "§r§7Kazdy particlesy",
            "§r§7Mozesz ukryc wszystkie",
            "§r§7particle pod /ustawienia",
            ""
        ], true);

        $loreCreator->alignCustomName(64);
        $loreCreator->alignLore();

        $item->setCustomName($loreCreator->getCustomName());
        $item->setLore($loreCreator->getLore());

        return $item;
    }
}