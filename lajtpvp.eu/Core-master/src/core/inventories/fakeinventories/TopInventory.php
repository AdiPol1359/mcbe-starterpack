<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories;

use core\inventories\FakeInventory;
use core\Main;
use core\utils\LoreCreator;
use core\utils\Settings;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class TopInventory extends FakeInventory {

    public function __construct() {
        parent::__construct("§l§eTOPKA");
    }

    public function setItems() : void {
        $this->fill();
        $players = Main::getInstance()->getUserManager()->getUsers();
        $guilds = Main::getInstance()->getGuildManager()->getGuilds();

        foreach(Settings::$TOP_INVENTORY as $key => $data) {
            $lore = [];

            $item = $data["item"];
            $item->setCustomName("§7[§8---===§7[ §e".$data["name"]."§7 ]§8===---§7]");

            $loreCreator = new LoreCreator($item->getCustomName(), []);

            $lore[] = "";

            usort($players,$data["callback"]);
            $top = array_reverse(array_slice($players, -10, 10, true), true);

            $index = 1;

            foreach($top as $user) {
                if($index >= 11)
                    break;

                $lore[] = "§r§7" . $index . ". §e" . $user->getName() . " §8(§7" . $data["result"]($user) . "§8)";
                $index++;
            }

            $lore[] = "";

            $lore = array_map(function (string $entry) : string{
                return $entry;
            }, array_values($lore));

            $loreCreator->setLore($lore);
            $loreCreator->alignLore();
            $item->setLore($loreCreator->getLore());

            $this->setItem($data["slot"], $item, true, true);
        }

        $lore = [];

        $item = VanillaItems::GOLD_INGOT();
        $item->setCustomName("§7[§8---===§7[ §eTOP GILDII§7 ]§8===---§7]");

        $loreCreator = new LoreCreator($item->getCustomName(), []);

        $lore[] = "";

        usort($guilds,fn($a, $b) => $a->getPoints() - $b->getPoints());
        $top = array_reverse(array_slice($guilds, -10, 10, true), true);

        $index = 1;

        foreach($top as $guild) {
            if($index >= 11)
                break;

            $lore[] = "§r§7" . $index . ". §e" . $guild->getTag() . " §8(§7" . $guild->getPoints() . "§8)";
            $index++;
        }

        $lore[] = "";

        $lore = array_map(function (string $entry) : string{
            return $entry;
        }, array_values($lore));

        $loreCreator->setLore($lore);
        $loreCreator->alignLore();
        $item->setLore($loreCreator->getLore());

        $this->setItem(20, $item, true, true);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {
        $this->unClickItem($player);
        return true;
    }
}