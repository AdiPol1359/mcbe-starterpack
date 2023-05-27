<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories\guild\panel;

use core\inventories\FakeInventory;
use core\guilds\Guild;
use core\guilds\GuildPlayer;
use core\Main;
use core\utils\LoreCreator;
use core\utils\StatsUtil;
use core\utils\Settings;
use core\utils\ItemUtil;
use core\utils\MessageUtil;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class ManagePlayerInventory extends FakeInventory {

    public function __construct(private Player $player, private Guild $guild, private string $playerName) {
        parent::__construct("§l§ePANEL");
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

        $guildPlayer = $this->guild->getPlayer($this->playerName);

        if(!$guildPlayer)
            return;

        $newAdvancement = $guildPlayer->getRank() === GuildPlayer::MEMBER ? GuildPlayer::OFFICER : GuildPlayer::LEADER;

        $advancement = $itemFactory->get(ItemIds::CONCRETE, 5);
        $advancement->setCustomName("§l§aAWANSUJ §r§8(§7".$newAdvancement."§8)");

        $this->setItem(10, $advancement, true, true);

        $degradation = $itemFactory->get(ItemIds::CONCRETE, 14);
        $degradation->setCustomName("§l§cDEGRADUJ §r§8(§7Czlonek§8)");

        $this->setItem(11, $degradation, true, true);

        $book = $itemFactory->get(ItemIds::BOOK);

        switch($guildPlayer->getRank()) {
            case GuildPlayer::MEMBER:
                $book->setCount(1);
                break;
            case GuildPlayer::OFFICER:
                $book->setCount(2);
                break;
            case GuildPlayer::LEADER:
                $book->setCount(3);
                break;
        }

        $book->setCustomName("§l§e".$guildPlayer->getRank());
        $this->setItem(16, $book, true, true);

        $bookshelf = $itemFactory->get(ItemIds::BOOKSHELF);
        $bookshelf->setCustomName("§l§ePERMISJE");

        $this->setItem(13, $bookshelf, true, true);

        $head = $itemFactory->get(ItemIds::MOB_HEAD, 3);

        if($guildPlayer->getRank() === GuildPlayer::OFFICER || $guildPlayer->getRank() === GuildPlayer::LEADER)
            ItemUtil::addItemGlow($head);

        $head->setCustomName("§7[§8----====§7[ §e".$guildPlayer->getName()."§r§7 ]§8====----§7]");

        $user = Main::getInstance()->getUserManager()->getUser($guildPlayer->getName());
        $statManager = $user->getStatManager();

        $head->setLore([
            "",
            "§r§7Ranga w gildii §e".$guildPlayer->getRank(),
            "§r§7Punkty gracza §e".($statManager->getStat(Settings::$STAT_POINTS)),
            "§r§7Zabojstwa gracza §e".($statManager->getStat(Settings::$STAT_KILLS)),
            "§r§7Smierci gracza §e".($statManager->getStat(Settings::$STAT_DEATHS)),
            ""
        ]);

        $loreCreator = new LoreCreator($head->getCustomName(), $head->getLore());
        $loreCreator->alignLore();
        $head->setLore($loreCreator->getLore());
        $head->getNamedTag()->setString("guildPlayerName", $guildPlayer->getName());

        $this->setItem(15, $head, true, true);

        $this->setItem(22, $itemFactory->get(ItemIds::NETHER_STAR)->setCustomName("§l§cPOWROT"), true, true);
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
            $this->changeInventory($player, (new ChoosePlayerInventory($player, $this->guild)));

        if($sourceItem->getId() === ItemIds::BOOKSHELF)
            $this->changeInventory($player, (new PlayerPermissionsInventory($player, $this->guild, $this->playerName)));

        if($sourceItem->getId() === ItemIds::CONCRETE) {

            $guildPlayer = $this->guild->getPlayer($this->playerName);
            $giver = $this->guild->getPlayer($player->getName());

            if(!$this->player->hasPermission(Settings::$PERMISSION_TAG . "command.guildadmin") && $guildPlayer->getRank() === GuildPlayer::LEADER) {
                $player->sendMessage(MessageUtil::format("Nie mozna zarzadzac liderem w panelu!"));
                $this->closeFor($player);
                return true;
            }

            if($guildPlayer) {
                switch($sourceItem->getMeta()) {
                    case 5:
                        if($guildPlayer->getRank() === GuildPlayer::LEADER) {
                            $this->unClickItem($player);
                            return true;
                        }

                        $newAdvancement = $guildPlayer->getRank() === GuildPlayer::MEMBER ? GuildPlayer::OFFICER : GuildPlayer::LEADER;

                        if(!$this->player->hasPermission(Settings::$PERMISSION_TAG . "command.guildadmin") && $newAdvancement === GuildPlayer::LEADER && $giver->getRank() !== GuildPlayer::LEADER) {
                            $this->closeFor($player);
                            $player->sendMessage(MessageUtil::format("Lidera gildii moze ustawic wylacznie aktualny lider"));
                            return true;
                        }

                        $guildPlayer->setRank($newAdvancement);

                        if($newAdvancement === GuildPlayer::LEADER) {
                            $guildPlayer->setAllSettings(true);
                            $giver->setDefaultSettings();

                            $giver->setRank(GuildPlayer::MEMBER);
                            $this->closeFor($player);
                        }

                        if($guildPlayer->getRank() === GuildPlayer::LEADER) {
                            foreach($this->guild->getPlayers() as $gPlayer) {
                                if($gPlayer->getRank() === GuildPlayer::LEADER && $gPlayer->getName() !== $this->playerName)
                                    $gPlayer->setRank(GuildPlayer::MEMBER);
                            }
                        }
                        
                        break;

                    case 14:
                        $guildPlayer->setRank(GuildPlayer::MEMBER);
                        break;
                }

                $this->setItems();
            }
        }

        $this->unClickItem($player);
        return true;
    }
}