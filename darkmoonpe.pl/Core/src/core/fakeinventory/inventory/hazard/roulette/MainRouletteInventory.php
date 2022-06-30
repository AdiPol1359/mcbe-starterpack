<?php

namespace core\fakeinventory\inventory\hazard\roulette;

use core\fakeinventory\FakeInventory;
use core\fakeinventory\inventory\hazard\HazardInventory;
use core\manager\managers\hazard\types\roulette\RouletteGame;
use core\manager\managers\hazard\types\roulette\RouletteManager;
use core\manager\managers\item\LoreCreator;
use core\manager\managers\PacketManager;
use core\manager\managers\ServerManager;
use core\manager\managers\SoundManager;
use core\util\utils\MessageUtil;
use core\util\utils\StringUtil;
use core\util\utils\TimeUtil;
use pocketmine\item\Item;
use pocketmine\Player;

class MainRouletteInventory extends FakeInventory {

    public function __construct(Player $player) {
        parent::__construct($player, "§l§9RULETKA", self::SMALL);

        $this->setItems();
    }

    public function setItems() : void {

        $this->fillBars();

        $money = Item::get(Item::GOLD_INGOT);

        $money->setCustomName(StringUtil::correctText("§9§lBETOWANIE", 43));

        $info = Item::get(Item::EXPERIENCE_BOTTLE);

        $info->setCustomName(StringUtil::correctText("§r§7[§8---===§7[ §9§lINFORMACJE§r§7 ]§8===---§7]", 44));

        $black = [0, 0];
        $red = [0, 0];
        $green = [0, 0];

        foreach(RouletteGame::getPlayers() as $nick => $roulettePlayer){

            foreach($roulettePlayer->getBetAmounts() as $color => $amount){

                if($color === "Czarny") {
                    $black[1] += $amount;
                    $black[0]++;
                }

                if($color === "Czerwony") {
                    $red[1] += $amount;
                    $red[0]++;
                }

                if($color === "Zielony") {
                    $green[1] += $amount;
                    $green[0]++;
                }
            }
        }


        $loreCreator = new LoreCreator();
        $loreCreator->setCustomName($info->getCustomName(), true);
        $loreCreator->setLore([
            "",
            "§r§8(laczne sumy graczy)",
            "§r§7Czarny: §9".$black[1]."§8zl",
            "§r§7Czerwony: §9".$red[1]."§8zl",
            "§r§7Zielony: §9".$green[1]."§8zl",
            "",
            "§r§8(laczna ilosc graczy)",
            "§r§7Czarny: §9".$black[0],
            "§r§7Czerwony: §9".$red[0],
            "§r§7Zielony: §9".$green[0]
        ], true);

        $loreCreator->alignCustomName(64);
        $loreCreator->alignLore();

        $info->setCustomName($loreCreator->getCustomName());
        $info->setLore($loreCreator->getLore());

        $roulette = Item::get(Item::CLOCK);
        $roulette->setCustomName("§r§7[§8---===§7[ §9§lLOSOWANIE§r§7 ]§8===---§7]");

        $loreCreator->setCustomName($roulette->getCustomName(), true);
        $loreCreator->setLore([
            "",
            "§r§7Ogladanie: §9".(RouletteGame::hasGameStarted() ? "§aDOSTEPNE" : "§cNIEDOSTEPNE"),
            "§r§7Start za: §8(".TimeUtil::convertIntToStringTime((RouletteGame::getTime() - time()), "§9", "§7", true, false)."§8)",
        ], true);

        $loreCreator->alignCustomName(51);
        $loreCreator->alignLore();

        $roulette->setCustomName($loreCreator->getCustomName());
        $roulette->setLore($loreCreator->getLore());

        $netherStar = Item::get(Item::NETHER_STAR)->setCustomName("§l§cPOWORT");

        $this->setItemAt(2, 2, $money);
        $this->setItemAt(5, 2, $info);
        $this->setItemAt(8, 2, $roulette);
        $this->setItemAt(5, 3, $netherStar);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== Item::IRON_BARS)
            SoundManager::addSound($player, $this->holder, "random.click");

        if($sourceItem->getId() === Item::NETHER_STAR)
            (new HazardInventory($player))->openFor([$player]);

        switch($sourceItem->getId()) {

            case Item::GOLD_INGOT:

                if(!ServerManager::isSettingEnabled(ServerManager::HAZARD)) {
                    $this->closeFor($player);
                    $player->sendMessage(MessageUtil::format("Hazard jest aktualnie wylaczony!"));
                    return true;
                }

                if(!RouletteGame::isLocked())
                    (new ChooseColor($player))->openFor([$player]);
                else{
                    $this->close($player);
                    $player->sendMessage(MessageUtil::format("Ruletka jest zablokowana poniewaz zaraz rozpocznie sie losowanie, musisz podczekac do konca gry!"));
                }

                break;

            case Item::EXPERIENCE_BOTTLE:
                break;

            case Item::CLOCK:
                if(RouletteGame::hasGameStarted())
                    RouletteManager::openRoulette($player);
                break;
        }

        PacketManager::unClickButton($player);
        return true;
    }
}