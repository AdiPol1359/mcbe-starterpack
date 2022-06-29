<?php

namespace core\fakeinventory\inventory\hazard\roulette;

use core\fakeinventory\FakeInventory;
use core\manager\managers\hazard\types\roulette\RouletteGame;
use core\manager\managers\PacketManager;
use core\manager\managers\ServerManager;
use core\manager\managers\SoundManager;
use core\util\utils\MessageUtil;
use pocketmine\item\Item;
use pocketmine\Player;

class ChooseColor extends FakeInventory {

    public function __construct(Player $player) {
        parent::__construct($player, "§9§lHAZARD", self::SMALL);
        $this->setItems();
    }

    public function setItems() : void {

        $this->fillBars();

        $roulettePlayer = RouletteGame::getPlayer($this->player->getName());

        $roulettePlayer ? $bets = $roulettePlayer->getBetAmounts() : $bets = [];

        $red = Item::get(Item::CONCRETE, 14)->setCustomName("§7Czerwony: §l§9".($bets["Czerwony"] ?? "0")."§r§7zl");
        $black = Item::get(Item::CONCRETE, 15)->setCustomName("§7Czarny§: §l§9".($bets["Czarny"] ?? "0")."§r§7zl");
        $green = Item::get(Item::CONCRETE, 5)->setCustomName("§7Zielony: §l§9".($bets["Zielony"] ?? "0")."§r§7zl");
        $netherStar = Item::get(Item::NETHER_STAR)->setCustomName("§l§cPOWORT");

        $this->setItemAt(3, 2, $red);
        $this->setItemAt(5, 2, $black);
        $this->setItemAt(7, 2, $green);
        $this->setItemAt(5, 3, $netherStar);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if($sourceItem->getId() !== Item::IRON_BARS)
            SoundManager::addSound($player, $this->holder, "random.click");

        if(!ServerManager::isSettingEnabled(ServerManager::HAZARD)) {
            $this->closeFor($player);
            $player->sendMessage(MessageUtil::format("Hazard jest aktualnie wylaczony!"));
            return true;
        }

        $roulettePlayer = RouletteGame::getPlayer($this->player->getName());

        $roulettePlayer ? $bets = $roulettePlayer->getBetAmounts() : $bets = [];

        if($sourceItem->getId() === Item::CONCRETE) {

            $namedtag = $sourceItem->getNamedTag();

            switch($sourceItem->getDamage()) {
                case 14:

                    $namedtag->setString("BetColor", "Czerwony");
                    $namedtag->setFloat("BetAmount", ($bets["Czerwony"] ?? 0));

                    break;

                case 15:

                    $namedtag->setString("BetColor", "Czarny");
                    $namedtag->setFloat("BetAmount", ($bets["Czarny"] ?? 0));

                    break;

                case 5:

                    $namedtag->setString("BetColor", "Zielony");
                    $namedtag->setFloat("BetAmount", ($bets["Zielony"] ?? 0));

                    break;
            }

            if($namedtag->hasTag("BetColor") && $namedtag->hasTag("BetAmount"))
                (new ManageBetInventory($player, $sourceItem))->openFor([$player]);
        }

        if($sourceItem->getId() === Item::NETHER_STAR)
            (new MainRouletteInventory($player))->openFor([$player]);

        PacketManager::unClickButton($player);
        return true;
    }
}