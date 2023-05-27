<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories\guild\panel;

use core\inventories\FakeInventory;
use core\inventories\FakeInventoryPatterns;
use core\guilds\Guild;
use core\guilds\GuildPlayer;
use core\inventories\FakeInventorySize;
use core\Main;
use core\utils\CustomItemUtil;
use core\utils\LoreCreator;
use core\utils\Settings;
use core\utils\ItemUtil;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class ChoosePlayerInventory extends FakeInventory {

    private int $page;
    private array $players = [];

    public function __construct(private Player $player, private Guild $guild) {
        $this->page = 1;

        parent::__construct("§l§ePANEL", FakeInventorySize::LARGE_CHEST);
    }

    public function onOpen(Player $who) : void {
        $this->page = 1;
        $this->players[$this->page] = [];
        parent::onOpen($who);
    }

    public function setItems() : void{
        $itemFactory = ItemFactory::getInstance();
        
        $this->clearAll();

        $this->fillWithPattern(FakeInventoryPatterns::PATTERN_FILL_UP_AND_DOWN);
        $this->updatePlayers();

        if(!$this->guild)
            return;

        if(!isset($this->players[$this->page]))
            $this->page = max(1, $this->page - 1);

        $lastSlot = 9;

        if(isset($this->players[$this->page])) {
            foreach ($this->players[$this->page] as $backup) {
                $this->setPlayer($backup, $lastSlot);
                $lastSlot++;
            }
        }

        if(!$this->guild->existsPlayer($this->player->getName())) {
            if(!$this->player->hasPermission(Settings::$PERMISSION_TAG . "command.guildadmin"))
                return;
        }

        $pageBack = new CustomItemUtil(ItemIds::CONCRETE);
        $pageBack->setCustomName("POPRZEDNIA STRONA");
        if($this->page <= 1) {
            $pageBack->setMeta(14);
            $pageBack->setCustomName("§c".$pageBack->getCustomName());
        } else {
            $pageBack->setCustomName("§a".$pageBack->getCustomName());
            $pageBack->setMeta(5);
        }

        $pageNext = new CustomItemUtil(ItemIds::CONCRETE);
        $pageNext->setCustomName("NASTEPNA STRONA");
        if(max(array_keys($this->players)) > $this->page) {
            $pageNext->setMeta(5);
            $pageNext->setCustomName("§a".$pageNext->getCustomName());
        }else {
            $pageNext->setCustomName("§c".$pageNext->getCustomName());
            $pageNext->setMeta(14);
        }

        $this->setItemAt(4, 6, $pageBack->getItem());
        $this->setItemAt(6, 6, $pageNext->getItem());

        $this->setItem(49, $itemFactory->get(ItemIds::NETHER_STAR)->setCustomName("§l§cPOWROT"), true, true);
    }

    public function updatePlayers() : void {

        $players = [
            1 => []
        ];

        $page = 1;

        $playersData = $this->guild->getPlayers();

        foreach($playersData as $playerData) {
            if(count($players[$page]) >= 36)
                $page++;

            if(!isset($players[$page]))
                $players[$page] = [];

            $players[$page][] = $playerData;
        }

        $this->players = $players;
    }

    public function setPlayer(GuildPlayer $player, int $playersSlot) : void {
        $itemFactory = ItemFactory::getInstance();
        $head = $itemFactory->get(ItemIds::MOB_HEAD, 3);

        if($player->getRank() === GuildPlayer::OFFICER || $player->getRank() === GuildPlayer::LEADER)
            ItemUtil::addItemGlow($head);

        $head->setCustomName("§7[§8----====§7[ §e".$player->getName()."§r§7 ]§8====----§7]");

        $user = Main::getInstance()->getUserManager()->getUser($player->getName());
        $statManager = $user->getStatManager();

        $head->setLore([
            "",
            "§r§7Ranga w gildii §e".$player->getRank(),
            "§r§7Punkty gracza §e".($statManager->getStat(Settings::$STAT_POINTS)),
            "§r§7Zabojstwa gracza §e".($statManager->getStat(Settings::$STAT_KILLS)),
            "§r§7Smierci gracza §e".($statManager->getStat(Settings::$STAT_DEATHS)),
            ""
        ]);

        $loreCreator = new LoreCreator($head->getCustomName(), $head->getLore());
        $loreCreator->alignLore();
        $head->setLore($loreCreator->getLore());
        $head->getNamedTag()->setString("guildPlayerName", $player->getName());

        $this->setItem($playersSlot, $head, true, true);
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

        if($sourceItem->getId() === ItemIds::NETHER_STAR)
            $this->changeInventory($player, (new MainPanelInventory($player, $this->guild)));

        if($sourceItem->getId() === ItemIds::MOB_HEAD) {

            $namedTag =  $sourceItem->getNamedTag();

            if($namedTag->getTag("guildPlayerName")) {

                $playerName = $namedTag->getString("guildPlayerName");

                $this->changeInventory($player, (new ManagePlayerInventory($player, $this->guild, $playerName)));
            }
        }

        if($sourceItem->getId() === ItemIds::CONCRETE) {
            switch($slot) {
                case 48:
                    $this->page--;
                    break;

                case 50:
                    $this->page++;
                    break;
            }
            $this->setItems();
        }

        $this->unClickItem($player);
        return true;
    }
}