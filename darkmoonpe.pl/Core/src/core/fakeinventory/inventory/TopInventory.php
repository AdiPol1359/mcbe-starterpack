<?php

namespace core\fakeinventory\inventory;

use core\fakeinventory\FakeInventory;
use core\manager\managers\item\LoreCreator;
use core\manager\managers\PacketManager;
use core\manager\managers\StatsManager;
use core\user\UserManager;
use core\util\utils\StringUtil;
use core\util\utils\TimeUtil;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;

class TopInventory extends FakeInventory {

    public function __construct(Player $player) {
        parent::__construct($player, "§l§9TOPKA", self::SMALL);

        $this->setItems();
    }

    public function setItems() : void {

        $this->fillBars();

        $players = UserManager::getUsers();

        // SMIERCI

        $deaths = Item::get(Item::TOTEM);

        $deaths->setCustomName(StringUtil::correctText("§r§7[§8---===§7[ §9§lTOPKA SMIERCI§r§7 ]§8===---§7]", 44));

        $loreCreator = new LoreCreator();
        $loreCreator->setCustomName($deaths->getCustomName(), true);

        $lore[] = "";

        $deathPlayers = $players;

        usort($deathPlayers,fn($a, $b) => $a->getStat(StatsManager::DEATHS) - $b->getStat(StatsManager::DEATHS));

        $top = array_reverse(array_slice($deathPlayers, -10, 10, true), true);

        $index = 1;

        foreach($top as $user) {
            if($index >= 11)
                break;

            $lore[] = "§r§7" . $index . ". §9" . $user->getName() . " §8(§7" . $user->getStat(StatsManager::DEATHS) . "§8)";
            $index++;
        }

        for($i = $index; $i <= 10; $i++)
            $lore[] = "§r§7" . $i . ". §9BRAK DANYCH";

        $lore[] = "";

        $lore = array_map(function (string $entry) : string{
            return $entry;
        }, array_values($lore));

        $loreCreator->setLore($lore);
        $loreCreator->alignCustomName(51);
        $loreCreator->alignLore();
        $deaths->setLore($loreCreator->getLore());
        $lore = [];

        // KILLE

        $diamondSword = Item::get(Item::DIAMOND_SWORD);
        $diamondSword->setCustomName(StringUtil::correctText("§r§7[§8---===§7[ §9§lTOPKA ZABOJSTW§r§7 ]§8===---§7]", 44));

        $loreCreator->setCustomName($diamondSword->getCustomName(), true);

        $lore[] = "";

        $killPlayers = $players;

        usort($killPlayers,fn($a, $b) => $a->getStat(StatsManager::KILLS) - $b->getStat(StatsManager::KILLS));

        $top = array_reverse(array_slice($killPlayers, -10, 10, true), true);

        $index = 1;

        foreach($top as $user) {
            if($index >= 11)
                break;

            $lore[] = "§r§7" . $index . ". §9" . $user->getName() . " §8(§7" . $user->getStat(StatsManager::KILLS) . "§8)";
            $index++;
        }

        for($i = $index; $i <= 10; $i++)
            $lore[] = "§r§7" . $i . ". §9BRAK DANYCH";

        $lore[] = "";

        $lore = array_map(function (string $entry) : string{
            return $entry;
        }, array_values($lore));

        $loreCreator->setLore($lore);
        $loreCreator->alignCustomName(51);
        $loreCreator->alignLore();
        $diamondSword->setLore($loreCreator->getLore());
        $lore = [];

        // SPEDZONY CZAS

        $time = Item::get(Item::FEATHER);

        $time->setCustomName(StringUtil::correctText("§r§7[§8---===§7[ §9§lTOPKA SPEDZONEGO CZASU§r§7 ]§8===---§7]", 44));

        $loreCreator->setCustomName($time->getCustomName(), true);

        $lore[] = "";

        $timePlayers = $players;

        usort($timePlayers,fn($a, $b) => (
            ($a->getStat(StatsManager::TIME_PLAYED) + (Server::getInstance()->getPlayerExact($a->getName()) ? (time() - $a->getStat(StatsManager::LAST_PLAYED)) : 0))
            - ($b->getStat(StatsManager::TIME_PLAYED) + (Server::getInstance()->getPlayerExact($b->getName()) ? (time() - $b->getStat(StatsManager::LAST_PLAYED)) : 0)))
        );

        $top = array_reverse(array_slice($timePlayers, -10, 10, true), true);

        $index = 1;

        foreach($top as $user) {
            if($index >= 11)
                break;

            $lore[] = "§r§7" . $index . ". §9" . $user->getName() . " §8(§7" . TimeUtil::convertIntToStringTime(($user->getStat(StatsManager::TIME_PLAYED) + ($user->getStat(StatsManager::TIME_PLAYED) + (Server::getInstance()->getPlayerExact($user->getName()) ? (time() - $user->getStat(StatsManager::LAST_PLAYED)) : 0))), "§9", "§7", true, false) . "§8)";
            $index++;
        }

        for($i = $index; $i <= 10; $i++)
            $lore[] = "§r§7" . $i . ". §9BRAK DANYCH";

        $lore[] = "";

        $lore = array_map(function (string $entry) : string{
            return $entry;
        }, array_values($lore));

        $loreCreator->setLore($lore);
        $loreCreator->alignCustomName(51);
        $loreCreator->alignLore();
        $time->setLore($loreCreator->getLore());
        $lore = [];

        //QUESTY

        $enchantedBook = Item::get(Item::ENCHANTED_BOOK);
        $enchantedBook->setCustomName(StringUtil::correctText("§r§7[§8---===§7[ §9§lTOPKA WYKONANYCH QUESTOW§r§7 ]§8===---§7]", 44));

        $loreCreator->setCustomName($enchantedBook->getCustomName(), true);

        $lore[] = "";

        $questPlayers = $players;

        usort($questPlayers,fn($a, $b) => $a->getDoneQuestCount() - $b->getDoneQuestCount());

        $top = array_reverse(array_slice($questPlayers, -10, 10, true), true);

        $index = 1;

        foreach($top as $user) {
            if($index >= 11)
                break;

            $lore[] = "§r§7" . $index . ". §9" . $user->getName() . " §8(§7" . $user->getDoneQuestCount() . "§8)";
            $index++;
        }

        for($i = $index; $i <= 10; $i++)
            $lore[] = "§r§7" . $i . ". §9BRAK DANYCH";

        $lore[] = "";

        $lore = array_map(function (string $entry) : string{
            return $entry;
        }, array_values($lore));

        $loreCreator->setLore($lore);
        $loreCreator->alignCustomName(51);
        $loreCreator->alignLore();
        $enchantedBook->setLore($loreCreator->getLore());
        $lore = [];

        //ASYSTY

        $goldSword = Item::get(Item::GOLD_SWORD);
        $goldSword->setCustomName(StringUtil::correctText("§r§7[§8---===§7[ §9§lTOPKA ASYST§r§7 ]§8===---§7]", 44));

        $loreCreator->setCustomName($goldSword->getCustomName(), true);

        $lore[] = "";

        $assistsPlayers = $players;

        usort($assistsPlayers,fn($a, $b) => $a->getStat(StatsManager::ASSISTS) - $b->getStat(StatsManager::ASSISTS));

        $top = array_reverse(array_slice($assistsPlayers, -10, 10, true), true);

        $index = 1;

        foreach($top as $user) {
            if($index >= 11)
                break;

            $lore[] = "§r§7" . $index . ". §9" . $user->getName() . " §8(§7" . $user->getStat(StatsManager::ASSISTS) . "§8)";
            $index++;
        }

        for($i = $index; $i <= 10; $i++)
            $lore[] = "§r§7" . $i . ". §9BRAK DANYCH";

        $lore[] = "";

        $lore = array_map(function (string $entry) : string{
            return $entry;
        }, array_values($lore));

        $loreCreator->setLore($lore);
        $loreCreator->alignCustomName(51);
        $loreCreator->alignLore();
        $goldSword->setLore($loreCreator->getLore());
        $lore = [];

        //MONEY

        $gold = Item::get(Item::GOLD_INGOT);
        $gold->setCustomName(StringUtil::correctText("§r§7[§8---===§7[ §9§lTOPKA PIENIEDZY§r§7 ]§8===---§7]", 44));

        $loreCreator->setCustomName($gold->getCustomName(), true);

        $lore[] = "";

        $moneyPlayers = $players;

        usort($moneyPlayers,fn($a, $b) => $a->getPlayerMoney() - $b->getPlayerMoney());

        $top = array_reverse(array_slice($moneyPlayers, -10, 10, true), true);

        $index = 1;

        foreach($top as $user) {
            if($index >= 11)
                break;

            $lore[] = "§r§7" . $index . ". §9" . $user->getName() . " §8(§7" . $user->getPlayerMoney() . "zl§8)";
            $index++;
        }

        for($i = $index; $i <= 10; $i++)
            $lore[] = "§r§7" . $i . ". §9BRAK DANYCH";

        $lore[] = "";

        $lore = array_map(function (string $entry) : string{
            return $entry;
        }, array_values($lore));

        $loreCreator->setLore($lore);
        $loreCreator->alignCustomName(51);
        $loreCreator->alignLore();
        $gold->setLore($loreCreator->getLore());
        $lore = [];

        // BREAK BLOCKS

        $pickaxe = Item::get(Item::DIAMOND_PICKAXE);
        $pickaxe->setCustomName(StringUtil::correctText("§r§7[§8---===§7[ §9§lTOPKA WYKOPANYCH BLOKOW§r§7 ]§8===---§7]", 44));

        $loreCreator->setCustomName($pickaxe->getCustomName(), true);

        $lore[] = "";

        $breakPlayers = $players;

        usort($breakPlayers,fn($a, $b) => $a->getCobble() - $b->getCobble());

        $top = array_reverse(array_slice($breakPlayers, -10, 10, true), true);

        $index = 1;

        foreach($top as $user) {
            if($index >= 11)
                break;

            $lore[] = "§r§7" . $index . ". §9" . $user->getName() . " §8(§7" . $user->getCobble() . "§8)";
            $index++;
        }

        for($i = $index; $i <= 10; $i++)
            $lore[] = "§r§7" . $i . ". §9BRAK DANYCH";

        $lore[] = "";

        $lore = array_map(function (string $entry) : string{
            return $entry;
        }, array_values($lore));

        $loreCreator->setLore($lore);
        $loreCreator->alignCustomName(51);
        $loreCreator->alignLore();
        $pickaxe->setLore($loreCreator->getLore());

        $this->setItem(2, $deaths);
        $this->setItem(6, $diamondSword);
        $this->setItem(10, $time);
        $this->setItem(13, $enchantedBook);
        $this->setItem(16, $goldSword);
        $this->setItem(20,$gold);
        $this->setItem(24, $pickaxe);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        PacketManager::unClickButton($player);
        return true;
    }
}