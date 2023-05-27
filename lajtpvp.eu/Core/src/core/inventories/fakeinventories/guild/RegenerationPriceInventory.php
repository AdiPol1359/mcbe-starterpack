<?php

namespace core\inventories\fakeinventories\guild;

use core\inventories\FakeInventory;
use core\guilds\Guild;
use core\utils\LoreCreator;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class RegenerationPriceInventory extends FakeInventory {

    public function __construct(private Player $player, private Guild $guild) {
        parent::__construct("§l§eREGENERACJA");
    }

    public function setItems() : void{
        $itemFactory = ItemFactory::getInstance();
        $this->fill();

        if(!$this->guild)
            return;

        if(!$this->guild->existsPlayer($this->player->getName())) {
            if(!$this->player->hasPermission(Settings::$PERMISSION_TAG."command.guildadmin"))
                return;
        }

        $bottle = $itemFactory->get(ItemIds::BOTTLE_O_ENCHANTING);
        $bottle->setCustomName("§7[§8----====§7[ §eREGENERACJA§r§7 ]§8====----§7]");

        $bottle->setLore([
            "",
            "  §r§7Wplacono zloto §e"."§8(§e".$this->guild->getRegenerationGold()."§8)",
            " §r§7Czas regeneracji §e".date("H:i:s", (0.15 * count($this->guild->getRegenerationBlocks()))),
            "§r§7Koszt regeneracji (zloto) §e".floor(count($this->guild->getRegenerationBlocks()) / Settings::$GUILD_REGENERATION_COST),
            "    §r§7Ilosc blokow §e".(count($this->guild->getRegenerationBlocks()) ?? 0),
            "   §r§7Status §e".($this->guild->isRegenerationEnabled() ? "§aWlaczona" : "§cWylaczona"),
            ""
        ]);

        $loreCreator = new LoreCreator($bottle->getCustomName(), $bottle->getLore());
        $loreCreator->alignLore();
        $bottle->setLore($loreCreator->getLore());

        $this->setItem(11, ($itemFactory->get(ItemIds::STAINED_GLASS, 13)->setCustomName("§7Wplac §l§e5 §r§7zlota §8(§750 blokow§8)")), true, true);
        $this->setItem(12, ($itemFactory->get(ItemIds::STAINED_GLASS, 5)->setCustomName("§7Wplac §l§e10 §r§7zlota §8(§7100 bloki§8)")), true, true);
        $this->setItem(14, ($itemFactory->get(ItemIds::STAINED_GLASS, 1)->setCustomName("§7Wplac §l§e50 §r§7zlota §8(§7500 blokow§8)")), true, true);
        $this->setItem(15, ($itemFactory->get(ItemIds::STAINED_GLASS, 14)->setCustomName("§7Wplac §l§e100 §r§7zlota §8(§71000 blokow§8)")), true, true);

        $this->setItemAt(5, 2, $bottle);
        $this->setItemAt(5, 3, $itemFactory->get(ItemIds::NETHER_STAR)->setCustomName("§e§lPOWROT"));
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        if(!$this->guild) {
            $this->closeFor($player);
            return true;
        }

        if(!$this->guild->existsPlayer($this->player->getName())) {
            if(!$this->player->hasPermission(Settings::$PERMISSION_TAG . "command.guildadmin")) {
                $this->closeFor($player);
                return true;
            }
        }

        if($slot === 10) {

            if(count($this->guild->getRegenerationBlocks()) > 0) {
                $this->guild->setRegeneration($this->guild->isRegenerationEnabled() ? 0 : 1);
                $this->setItems();
            } else {
                $this->closeFor($player);
                $player->sendMessage(MessageUtil::format("Nie masz zadnych blokow do zregenerowania!"));
            }
        }

        if($sourceItem->getId() === ItemIds::NETHER_STAR)
            $this->changeInventory($player, (new MainRegenerationInventory($player, $this->guild)));

        if($sourceItem->getId() === ItemIds::STAINED_GLASS) {

            $gold = 0;

            $gold = match ($sourceItem->getMeta()) {
                13 => 5,
                5 => 10,
                1 => 50,
                14 => 100,
            };

            if($gold > 0) {
                $goldItem = ItemFactory::getInstance()->get(ItemIds::GOLD_INGOT, 0, $gold);

                if($player->getInventory()->contains($goldItem)) {
                    $player->getInventory()->removeItem($goldItem);
                    $this->guild->addRegenerationGold($gold);

                    $this->setItems();
                }
            }
        }
        $this->unClickItem($player);
        return true;
    }
}