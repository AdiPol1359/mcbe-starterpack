<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories\guild\panel;

use core\inventories\FakeInventory;
use core\guilds\Guild;
use core\guilds\GuildPlayer;
use core\utils\LoreCreator;
use core\utils\Settings;
use core\utils\MessageUtil;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\Server;

class MainPanelInventory extends FakeInventory {

    public function __construct(private Player $player, private Guild $guild) {
        parent::__construct("§e§lPANEL");
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

        $heart = $itemFactory->get(ItemIds::HEART_OF_THE_SEA);
        $heart->setCustomName("§7[§8---===§7[ §ePOWIEKSZANIE TERENU§7 ]§8===---§7]");

        $loreCreatorHeart = new LoreCreator();
        $loreCreatorHeart->setCustomName($heart->getCustomName(), true);
        $loreCreatorHeart->setLore([
            "",
            "§r§7Aktualna wielkosc terenu §e".$this->guild->getSize()."§7x"."§e".$this->guild->getSize(),
            "§r§7Maksymalny teren wynosi §e".Settings::$MAX_GUILD_SIZE."§7x"."§e".Settings::$MAX_GUILD_SIZE,
            "§r§7Teren sie powieksza o §e".Settings::$GUILD_TERRAIN_UPGRADE."§7 kratek",
            "§r§7Koszt ulepszenia terenu wynosi",
            "  §r§e64 §7emeraldy",
            "§r§8Kliknij aby ulepszyc teren",
            ""
        ], true);

        $loreCreatorHeart->alignLore();
        $heart->setLore($loreCreatorHeart->getLore());

        $this->setItemAt(2,2, $heart);

        $players = $itemFactory->get(ItemIds::MOB_HEAD, 3);
        $players->setCustomName("§7[§8---===§7[ §eZARZADZANIE GRACZMI§7 ]§8===---§7]");

        $onlineGuildPlayers = 0;
        $offlineGuildPlayers = 0;
        $officers = 0;
        $members = 0;

        foreach($this->guild->getPlayers() as $guildPlayerNick => $guildPlayer) {
            if(Server::getInstance()->getPlayerExact($guildPlayer->getName()))
                $onlineGuildPlayers++;
            else
                $offlineGuildPlayers++;

            if($guildPlayer->getRank() === GuildPlayer::MEMBER)
                $members++;

            if($guildPlayer->getRank() === GuildPlayer::OFFICER)
                $officers++;
        }

        $loreCreatorPlayers = new LoreCreator();
        $loreCreatorPlayers->setCustomName($heart->getCustomName(), true);
        $loreCreatorPlayers->setLore([
            "",
            "§r§7Status graczy w gildii §8("."§a".$onlineGuildPlayers."§7/§c".$offlineGuildPlayers."§7/§e".count($this->guild->getPlayers())."§8)",
            "     §r§7Czlonkow w gildii §e".$members,
            "     §r§7Oficerow w gildii §e".$officers,
            "   §r§7Limit slotow w gildii §e".$this->guild->getSlots()."§8/§e".Settings::$GUILD_MEMBERS_LIMIT,
            "§r§8Kliknij aby zarzadzac graczami",
            ""
        ], true);

        $loreCreatorPlayers->alignLore();
        $players->setLore($loreCreatorPlayers->getLore());

        $this->setItemAt(4,2, $players);

        $book = $itemFactory->get(ItemIds::BOOK);
        $book->setCustomName("§7[§8---===§7[ §ePRZEDLUZANIE WAZNOSCI§7 ]§8===---§7]");

        $loreCreatorBook = new LoreCreator();
        $loreCreatorBook->setCustomName($book->getCustomName(), true);
        $loreCreatorBook->setLore([
            "",
            "§r§7Gildia wygasa §e".(date("d.m.Y H:i:s", $this->guild->getExpireTime())),
            "§r§7Maksymalny czas §e".(date("d.m.Y H:i:s", time() + Settings::$MAX_EXPIRE_TIME)),
            "§r§7Przedluzanie gildii o §e1§7 dzien",
            "§r§7Koszt przedluzenia waznosci",
            "  §r§e500 §7emeraldow",
            "§r§8Kliknij aby ulepszyc teren",
            ""
        ], true);

        $loreCreatorBook->alignLore();
        $book->setLore($loreCreatorBook->getLore());

        $this->setItemAt(6,2, $book);

        //

        $redStone = $itemFactory->get(ItemIds::BOOKSHELF);
        $redStone->setCustomName("§7[§8---===§7[ §eLIMIT CZLONKOW§7 ]§8===---§7]");

        $loreCreatorRedStone = new LoreCreator();
        $loreCreatorRedStone->setCustomName($redStone->getCustomName(), true);
        $loreCreatorRedStone->setLore([
            "",
            "§r§7Ilosc wolnych slotow §e".$this->guild->getSlots()."§8/§e".Settings::$GUILD_MEMBERS_LIMIT,
            "§r§7Ilosc slotow powieksza sie o §e1§7 slota",
            "§r§7Koszt ulepszenia wynosi",
            "  §r§e64 §7emeraldy",
            "§r§8Kliknij aby powiekszyc",
            ""
        ], true);

        $loreCreatorRedStone->alignLore();
        $redStone->setLore($loreCreatorRedStone->getLore());

        $this->setItemAt(8,2, $redStone);

        //
        $totem = $itemFactory->get(ItemIds::TOTEM);
        $totem->setCustomName("Ochroniarz");

        $totem->setCustomName("§7[§8---===§7[ §eOCHRONIARZ§7 ]§8===---§7]");

        $loreCreatorTotem = new LoreCreator();
        $loreCreatorTotem->setCustomName($totem->getCustomName(), true);
        $loreCreatorTotem->setLore([
            "",
            "§r§7Aktualna zdrowie §e".$this->guild->getGolemHealth()."§8/"."§e".Settings::$GOLEM_MAX_HEALTH,
            "§r§7Zdrowie powieksza sie o §e".Settings::$GOLEM_UPGRADE_HEALTH."§7 hp",
            "§r§7Koszt ulepszenia wynosi",
            "  §r§e64 §7emeraldy",
            "§r§8Kliknij aby ulepszyc",
            ""
        ], true);

        $loreCreatorTotem->alignLore();
        $totem->setLore($loreCreatorTotem->getLore());

        $this->setItemAt(5,3, $totem);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {
        $itemFactory = ItemFactory::getInstance();

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

        if($sourceItem->getId() === ItemIds::TOTEM) {

            if($this->guild->getGolemHealth() < Settings::$GOLEM_MAX_HEALTH) {
                $emerald = $itemFactory->get(ItemIds::EMERALD, 0, 64);

                if($player->getInventory()->contains($emerald)) {
                    $player->getInventory()->removeItem($emerald);
                    $this->guild->addGolemHealth(Settings::$GOLEM_UPGRADE_HEALTH);
                    $this->setItems();
                } else {
                    $this->closeFor($player);
                    $player->sendMessage(MessageUtil::format("Nie masz wystarczajaco duzo emeraldow aby ulepszyc golema!"));
                }
            } else {
                $this->closeFor($player);
                $player->sendMessage(MessageUtil::format("Twoja gildia osiagnela limit zdrowia golema!"));
            }
        }

        if($sourceItem->getId() === ItemIds::BOOKSHELF) {

            if($this->guild->getSlots() < Settings::$GUILD_MEMBERS_LIMIT) {
                $emerald = $itemFactory->get(ItemIds::EMERALD, 0, 64);

                if($player->getInventory()->contains($emerald)) {
                    $player->getInventory()->removeItem($emerald);
                    $this->guild->addSlot();
                    $this->setItems();
                } else {
                    $this->closeFor($player);
                    $player->sendMessage(MessageUtil::format("Nie masz wystarczajaco duzo emeraldow aby powiekszyc ilosc slotow!"));
                }
            } else {
                $this->closeFor($player);
                $player->sendMessage(MessageUtil::format("Twoja gildia osiagnela limit slotow!"));
            }
        }

        if($sourceItem->getId() === ItemIds::HEART_OF_THE_SEA) {

            if($this->guild->getSize() < Settings::$MAX_GUILD_SIZE) {
                $emerald = $itemFactory->get(ItemIds::EMERALD, 0, 64);

                if($player->getInventory()->contains($emerald)) {
                    $player->getInventory()->removeItem($emerald);
                    $this->guild->setSize($this->guild->getSize() + Settings::$GUILD_TERRAIN_UPGRADE);
                    $this->setItems();
                } else {
                    $this->closeFor($player);
                    $player->sendMessage(MessageUtil::format("Nie masz wystarczajaco duzo emeraldow aby powiekszyc teren!"));
                }
            } else {
                $this->closeFor($player);
                $player->sendMessage(MessageUtil::format("Twoja gildia osiagnela limit wielkosci!"));
            }
        }

        if($sourceItem->getId() === ItemIds::BOOK) {

            if(($this->guild->getExpireTime()) < (time() + Settings::$MAX_EXPIRE_TIME) && ($this->guild->getExpireTime() + (3600 * 24)) < (time() + Settings::$MAX_EXPIRE_TIME)) {
                $emerald = $itemFactory->get(ItemIds::EMERALD, 0, 500);

                if($player->getInventory()->contains($emerald)) {
                    $player->getInventory()->removeItem($emerald);
                    $this->guild->setExpireTime($this->guild->getExpireTime() + (3600 * 24));
                    $this->setItems();
                } else {
                    $this->closeFor($player);
                    $player->sendMessage(MessageUtil::format("Nie masz wystarczajaco duzo emeraldow aby przedluzyc waznosc!"));
                }
            } else {
                $this->closeFor($player);
                $player->sendMessage(MessageUtil::format("Twoja gildia osiagnela limit waznosci!"));
            }
        }

        if($sourceItem->getId() === ItemIds::MOB_HEAD)
            $this->changeInventory($player, (new ChoosePlayerInventory($player, $this->guild)));

        $this->unClickItem($player);
        return true;
    }
}