<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories\guild\panel;

use core\inventories\FakeInventory;
use core\guilds\Guild;
use core\guilds\GuildPlayer;
use core\inventories\FakeInventorySize;
use core\utils\Settings;
use core\utils\ItemUtil;
use core\utils\MessageUtil;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;

class PlayerPermissionsInventory extends FakeInventory {

    private Guild $guild;
    private string $playerName;

    public function __construct(private Player $player, Guild $guild, string $playerName) {
        $this->guild = $guild;
        $this->playerName = $playerName;

        parent::__construct("§l§ePANEL", FakeInventorySize::LARGE_CHEST);
    }

    public function setItems() : void{
        $itemFactory = ItemFactory::getInstance();
        
        if(!$this->guild)
            return;

        if(!$this->guild->existsPlayer($this->player->getName())) {
            if(!$this->player->hasPermission(Settings::$PERMISSION_TAG."command.guildadmin"))
                return;
        }

        $ironBars = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 17, 18, 26, 27, 35, 36, 44, 45, 46, 47, 48, 50, 51, 52, 53];

        foreach($ironBars as $slot)
            $this->setItem($slot, $itemFactory->get(ItemIds::STAINED_GLASS_PANE, 7)->setCustomName(" "));

        $green = $itemFactory->get(ItemIds::CONCRETE, 5);
        $green->setCustomName("§l§aWLACZ WSZYSTKO");

        $red = $itemFactory->get(ItemIds::CONCRETE, 14);
        $red->setCustomName("§l§cWYLACZ WSZYSTKO");

        $this->setItem(48, $green, true, true);
        $this->setItem(50, $red, true, true);

        $this->setItem(49, $itemFactory->get(ItemIds::NETHER_STAR)->setCustomName("§l§cPOWROT"), true, true);

        //PERMISJE

        $player = $this->guild->getPlayer($this->playerName);

        $stonePickaxe = $itemFactory->get(ItemIds::STONE_PICKAXE)->setCustomName("§7Kopanie na terenie ");

        if($player->getSetting(GuildPlayer::BLOCK_BREAK)) {
            ItemUtil::addItemGlow($stonePickaxe);
            $stonePickaxe->setCustomName($stonePickaxe->getCustomName()."§l§aWlaczone");
        }else
            $stonePickaxe->setCustomName($stonePickaxe->getCustomName()."§l§cWylaczone");
        $stonePickaxe->getNamedTag()->setString("setting", GuildPlayer::BLOCK_BREAK);

        $diamondPickaxe = $itemFactory->get(ItemIds::DIAMOND_PICKAXE)->setCustomName("§7Niszczenie beacona ");

        if($player->getSetting(GuildPlayer::BEACON_BREAK)) {
            ItemUtil::addItemGlow($diamondPickaxe);
            $diamondPickaxe->setCustomName($diamondPickaxe->getCustomName()."§l§aWlaczone");
        }else
            $diamondPickaxe->setCustomName($diamondPickaxe->getCustomName()."§l§cWylaczone");
        $diamondPickaxe->getNamedTag()->setString("setting", GuildPlayer::BEACON_BREAK);

        $grass = $itemFactory->get(ItemIds::GRASS)->setCustomName("§7Stawianie blokow ");

        if($player->getSetting(GuildPlayer::BLOCK_PLACE)) {
            ItemUtil::addItemGlow($grass);
            $grass->setCustomName($grass->getCustomName()."§l§aWlaczone");
        }else
            $grass->setCustomName($grass->getCustomName()."§l§cWylaczone");
        $grass->getNamedTag()->setString("setting", GuildPlayer::BLOCK_PLACE);

        $tnt = $itemFactory->get(ItemIds::TNT)->setCustomName("§7Stawianie tnt ");

        if($player->getSetting(GuildPlayer::TNT_PLACE)) {
            ItemUtil::addItemGlow($tnt);
            $tnt->setCustomName($tnt->getCustomName()."§l§aWlaczone");
        }else
            $tnt->setCustomName($tnt->getCustomName()."§l§cWylaczone");
        $tnt->getNamedTag()->setString("setting", GuildPlayer::TNT_PLACE);

        $chest = $itemFactory->get(ItemIds::CHEST)->setCustomName("§7Interakcja ze skrzynkami ");

        if($player->getSetting(GuildPlayer::INTERACT_CHEST)) {
            ItemUtil::addItemGlow($chest);
            $chest->setCustomName($chest->getCustomName()."§l§aWlaczone");
        }else
            $chest->setCustomName($chest->getCustomName()."§l§cWylaczone");
        $chest->getNamedTag()->setString("setting", GuildPlayer::INTERACT_CHEST);

        $furnace = $itemFactory->get(ItemIds::FURNACE)->setCustomName("§7Interakcja z piecykiem ");

        if($player->getSetting(GuildPlayer::INTERACT_FURNACE)) {
            ItemUtil::addItemGlow($furnace);
            $furnace->setCustomName($furnace->getCustomName()."§l§aWlaczone");
        }else
            $furnace->setCustomName($furnace->getCustomName()."§l§cWylaczone");
        $furnace->getNamedTag()->setString("setting", GuildPlayer::INTERACT_FURNACE);

        $beacon = $itemFactory->get(ItemIds::BEACON)->setCustomName("§7Interakcja z beaconem ");

        if($player->getSetting(GuildPlayer::INTERACT_BEACON)) {
            ItemUtil::addItemGlow($beacon);
            $beacon->setCustomName($beacon->getCustomName()."§l§aWlaczone");
        }else
            $beacon->setCustomName($beacon->getCustomName()."§l§cWylaczone");
        $beacon->getNamedTag()->setString("setting", GuildPlayer::INTERACT_BEACON);

        $useCustomBlocks = $itemFactory->get(ItemIds::SAND)->setCustomName("§7Uzywanie bojek/kopaczy ");

        if($player->getSetting(GuildPlayer::USE_CUSTOM_BLOCKS)) {
            ItemUtil::addItemGlow($useCustomBlocks);
            $useCustomBlocks->setCustomName($useCustomBlocks->getCustomName()."§l§aWlaczone");
        }else
            $useCustomBlocks->setCustomName($useCustomBlocks->getCustomName()."§l§cWylaczone");
        $useCustomBlocks->getNamedTag()->setString("setting", GuildPlayer::USE_CUSTOM_BLOCKS);

        $addPlayer = $itemFactory->get(ItemIds::DYE, 10)->setCustomName("§7Dodawanie graczy ");

        if($player->getSetting(GuildPlayer::ADD_PLAYER)) {
            ItemUtil::addItemGlow($addPlayer);
            $addPlayer->setCustomName($addPlayer->getCustomName()."§l§aWlaczone");
        }else
            $addPlayer->setCustomName($addPlayer->getCustomName()."§l§cWylaczone");
        $addPlayer->getNamedTag()->setString("setting", GuildPlayer::ADD_PLAYER);

        $kickPlayer = $itemFactory->get(ItemIds::DYE, 1)->setCustomName("§7Wyrzucanie graczy ");

        if($player->getSetting(GuildPlayer::KICK_PLAYER)) {
            ItemUtil::addItemGlow($kickPlayer);
            $kickPlayer->setCustomName($kickPlayer->getCustomName()."§l§aWlaczone");
        }else
            $kickPlayer->setCustomName($kickPlayer->getCustomName()."§l§cWylaczone");
        $kickPlayer->getNamedTag()->setString("setting", GuildPlayer::KICK_PLAYER);

        $friendlyFire = $itemFactory->get(ItemIds::DIAMOND_SWORD)->setCustomName("§7Zmiana friendly fire ");

        if($player->getSetting(GuildPlayer::FRIENDLY_FIRE)) {
            ItemUtil::addItemGlow($friendlyFire);
            $friendlyFire->setCustomName($friendlyFire->getCustomName()."§l§aWlaczone");
        }else
            $friendlyFire->setCustomName($friendlyFire->getCustomName()."§l§cWylaczone");
        $friendlyFire->getNamedTag()->setString("setting", GuildPlayer::FRIENDLY_FIRE);

        $treasury = $itemFactory->get(ItemIds::ENDER_CHEST)->setCustomName("§7Skarbiec ");

        if($player->getSetting(GuildPlayer::TREASURY)) {
            ItemUtil::addItemGlow($treasury);
            $treasury->setCustomName($treasury->getCustomName()."§l§aWlaczone");
        }else
            $treasury->setCustomName($treasury->getCustomName()."§l§cWylaczone");
        $treasury->getNamedTag()->setString("setting", GuildPlayer::TREASURY);

        $panel = $itemFactory->get(ItemIds::TOTEM)->setCustomName("§7Dostep do panelu ");

        if($player->getSetting(GuildPlayer::PANEL)) {
            ItemUtil::addItemGlow($panel);
            $panel->setCustomName($panel->getCustomName()."§l§aWlaczone");
        }else
            $panel->setCustomName($panel->getCustomName()."§l§cWylaczone");
        $panel->getNamedTag()->setString("setting", GuildPlayer::PANEL);

        $regeneration = $itemFactory->get(ItemIds::MAGMA_CREAM)->setCustomName("§7Regeneracja ");

        if($player->getSetting(GuildPlayer::REGENERATION)) {
            ItemUtil::addItemGlow($regeneration);
            $regeneration->setCustomName($regeneration->getCustomName()."§l§aWlaczone");
        }else
            $regeneration->setCustomName($regeneration->getCustomName()."§l§cWylaczone");
        $regeneration->getNamedTag()->setString("setting", GuildPlayer::REGENERATION);

        $teleport = $itemFactory->get(ItemIds::ENDER_PEARL)->setCustomName("§7Teleport na teren ");

        if($player->getSetting(GuildPlayer::TELEPORT)) {
            ItemUtil::addItemGlow($teleport);
            $teleport->setCustomName($teleport->getCustomName()."§l§aWlaczone");
        }else
            $teleport->setCustomName($teleport->getCustomName()."§l§cWylaczone");
        $teleport->getNamedTag()->setString("setting", GuildPlayer::TELEPORT);

        $battle = $itemFactory->get(ItemIds::IRON_SWORD)->setCustomName("§7Walka ");

        if($player->getSetting(GuildPlayer::BATTLE)) {
            ItemUtil::addItemGlow($battle);
            $battle->setCustomName($battle->getCustomName()."§l§aWlaczone");
        }else
            $battle->setCustomName($battle->getCustomName()."§l§cWylaczone");
        $battle->getNamedTag()->setString("setting", GuildPlayer::BATTLE);

        $alliance = $itemFactory->get(ItemIds::BLAZE_POWDER)->setCustomName("§7Zarzadzanie sojuszami ");

        if($player->getSetting(GuildPlayer::ALLIANCE)) {
            ItemUtil::addItemGlow($alliance);
            $alliance->setCustomName($alliance->getCustomName()."§l§aWlaczone");
        }else
            $alliance->setCustomName($alliance->getCustomName()."§l§cWylaczone");
        $alliance->getNamedTag()->setString("setting", GuildPlayer::ALLIANCE);

        $alliancePvp = $itemFactory->get(ItemIds::GOLD_SWORD)->setCustomName("§7Pvp sojuszy ");

        if($player->getSetting(GuildPlayer::ALLIANCE_PVP)) {
            ItemUtil::addItemGlow($alliancePvp);
            $alliancePvp->setCustomName($alliancePvp->getCustomName()."§l§aWlaczone");
        }else
            $alliancePvp->setCustomName($alliancePvp->getCustomName()."§l§cWylaczone");
        $alliancePvp->getNamedTag()->setString("setting", GuildPlayer::ALLIANCE_PVP);

        $chestLocker = $itemFactory->get(ItemIds::CHEST)->setCustomName("§7Otwieranie zablokowanych skrzynek ");

        if($player->getSetting(GuildPlayer::CHEST_LOCKER)) {
            ItemUtil::addItemGlow($chestLocker);
            $chestLocker->setCustomName($chestLocker->getCustomName()."§l§aWlaczone");
        }else
            $chestLocker->setCustomName($chestLocker->getCustomName()."§l§cWylaczone");
        $chestLocker->getNamedTag()->setString("setting", GuildPlayer::CHEST_LOCKER);

        $this->setItem(10, $stonePickaxe, true, true);
        $this->setItem(11, $diamondPickaxe, true, true);
        $this->setItem(12, $grass, true, true);
        $this->setItem(13, $tnt, true, true);
        $this->setItem(14, $chest, true, true);
        $this->setItem(15, $furnace, true, true);
        $this->setItem(16, $beacon, true, true);
        $this->setItem(19, $useCustomBlocks, true, true);
        $this->setItem(20, $addPlayer, true, true);
        $this->setItem(21, $kickPlayer, true, true);
        $this->setItem(22, $friendlyFire, true, true);
        $this->setItem(23, $treasury, true, true);
        $this->setItem(24, $panel, true, true);
        $this->setItem(25, $regeneration, true, true);
        $this->setItem(28, $teleport, true, true);
        $this->setItem(29, $battle, true, true);
        $this->setItem(30, $alliance, true, true);
        $this->setItem(31, $alliancePvp, true, true);
        $this->setItem(32, $chestLocker, true, true);
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
            $this->changeInventory($player, (new ManagePlayerInventory($player, $this->guild, $this->playerName)));

        $guildPlayer = $this->guild->getPlayer($this->playerName);

        if(!$guildPlayer) {
            $this->closeFor($player);
            return true;
        }

        if($sourceItem->getId() === ItemIds::CONCRETE) {

            if(!$this->player->hasPermission(Settings::$PERMISSION_TAG . "command.guildadmin") && $guildPlayer->getRank() === GuildPlayer::LEADER) {
                $player->sendMessage(MessageUtil::format("Nie mozna zarzadzac liderem w panelu!"));
                $this->closeFor($player);
                return true;
            }

            switch($sourceItem->getMeta()) {

                case 5:
                    $guildPlayer->setAllSettings(true);
                    break;

                case 14:
                    $guildPlayer->setAllSettings(false);
                    break;

            }

            $this->setItems();
        }

        $namedTag = $sourceItem->getNamedTag();

        if($namedTag->getTag("setting")) {

            if(!$this->player->hasPermission(Settings::$PERMISSION_TAG . "command.guildadmin") && $guildPlayer->getRank() === GuildPlayer::LEADER) {
                $player->sendMessage(MessageUtil::format("Nie mozna zarzadzac liderem w panelu!"));
                $this->closeFor($player);
                return true;
            }

            $settingName = $namedTag->getString("setting");

            if($settingName)
                $guildPlayer->switchSetting($settingName);

            $this->setItems();
        }

        $this->unClickItem($player);
        return true;
    }
}