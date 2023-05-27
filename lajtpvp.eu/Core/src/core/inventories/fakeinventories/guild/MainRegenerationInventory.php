<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories\guild;

use core\inventories\FakeInventory;
use core\guilds\Guild;
use core\Main;
use core\utils\LoreCreator;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class MainRegenerationInventory extends FakeInventory {

    private Guild $guild;

    public function __construct(private Player $player, Guild $guild) {
        $this->guild = $guild;

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

        $concrete = $itemFactory->get(ItemIds::CONCRETE, ($this->guild->isRegenerationEnabled() ? 14 : 5));
        $concrete->setCustomName(($this->guild->isRegenerationEnabled() ? "§c§lZATRZYMYWANIE REGENERACJI" : "§l§aWLACZENIE REGENERACJI"));

        $bottle = $itemFactory->get(ItemIds::BOTTLE_O_ENCHANTING);
        $bottle->setCustomName("§7[§8----====§7[ §eREGENERACJA§r§7 ]§8====----§7]");

        $bottle->setLore([
            "",
            "  §r§7Wplacono zloto §e"."§8(§e".$this->guild->getRegenerationGold()."§8)",
            " §r§7Czas regeneracji §e".date("H:i:s", (int)(0.15 * count($this->guild->getRegenerationBlocks()))),
            "§r§7Koszt regeneracji (zloto) §e".floor(count($this->guild->getRegenerationBlocks()) / Settings::$GUILD_REGENERATION_COST),
            "    §r§7Ilosc blokow §e".(count($this->guild->getRegenerationBlocks()) ?? 0),
            "   §r§7Status §e".($this->guild->isRegenerationEnabled() ? "§aWlaczona" : "§cWylaczona"),
            ""
        ]);

        $loreCreator = new LoreCreator($bottle->getCustomName(), $bottle->getLore());
        $loreCreator->alignLore();
        $bottle->setLore($loreCreator->getLore());

        $star = $itemFactory->get(ItemIds::NETHER_STAR);
        $star->setCustomName("§l§eWPLAC ZLOTO");

        $this->setItemAt(2, 2, $concrete);
        $this->setItemAt(5, 2, $bottle);
        $this->setItemAt(8, 2, $star);
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

            if((Main::getInstance()->getWarManager()->getWar($this->guild->getTag()))) {
                $this->closeFor($player);
                $player->sendMessage(MessageUtil::format("Nie mozesz wlaczyc regeneracji podczas wojny!"));
                return true;
            }

            if($this->guild->getRegenerationGold() <= 0) {
                $this->closeFor($player);
                $player->sendMessage(MessageUtil::format("Nie masz wystarczajaco duzo zlota aby rozpoczac regeneracje!"));
                return true;
            } else {
                if(count($this->guild->getRegenerationBlocks()) > 0) {
                    $this->guild->setRegeneration($this->guild->isRegenerationEnabled());
                    $this->setItems();
                } else {
                    $this->closeFor($player);
                    $player->sendMessage(MessageUtil::format("Nie masz zadnych blokow do zregenerowania!"));
                    return true;
                }
            }
        }

        if($slot === 16)
            $this->changeInventory($player, (new RegenerationPriceInventory($player, $this->guild)));

        $this->unClickItem($player);
        return true;
    }
}