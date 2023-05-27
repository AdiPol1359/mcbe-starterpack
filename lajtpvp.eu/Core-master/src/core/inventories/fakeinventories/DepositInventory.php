<?php

declare(strict_types=1);

namespace core\inventories\fakeinventories;

use core\inventories\FakeInventory;
use core\items\custom\ThrownTNT;
use core\Main;
use core\utils\DepositUtil;
use core\utils\LoreCreator;
use core\utils\Settings;
use pocketmine\block\Transparent;
use pocketmine\item\EnderPearl;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class DepositInventory extends FakeInventory {

    public function __construct(private Player $player) {
        parent::__construct("§l§eDEPOZYT");
    }

    public function setItems() : void {
        $this->fill();

        if(!$this->player) {
            return;
        }

        $user = Main::getInstance()->getUserManager()->getUser($this->player->getName());
        $statManager = $user->getStatManager();

        $itemFactory = ItemFactory::getInstance();

        // REFILE

        $goldenApple = VanillaItems::GOLDEN_APPLE();
        $goldenApple->setCustomName("§7[§8----====§7[ §eREFILE§r§7 ]§8====----§7]");
        $goldenApple->setLore([
            "",
            "§r§7Limit serwerowy §e".Settings::$REFILE_LIMIT,
            "§r§7Posiadane refile §e".$statManager->getStat(Settings::$STAT_GOLDEN_APPLES),
        ]);

        $loreCreator = new LoreCreator($goldenApple->getCustomName(), $goldenApple->getLore());
        $loreCreator->alignLore();
        $goldenApple->setLore($loreCreator->getLore());

        $this->setItem(11, $goldenApple, true, true);

        // KOXY

        $enchantedApple = VanillaItems::ENCHANTED_GOLDEN_APPLE();
        $enchantedApple->setCustomName("§7[§8----====§7[ §eKOXY§r§7 ]§8====----§7]");
        $enchantedApple->setLore([
            "",
            " §r§7Limit serwerowy §e".Settings::$ENCHANTED_LIMIT,
            "§r§7Posiadane koxy §e".$statManager->getStat(Settings::$STAT_ENCHANTED_APPLES),
        ]);

        $loreCreator = new LoreCreator($enchantedApple->getCustomName(), $enchantedApple->getLore());
        $loreCreator->alignLore();
        $enchantedApple->setLore($loreCreator->getLore());

        $this->setItem(10, $enchantedApple, true, true);

        // PERLY

        $pearl = VanillaItems::ENDER_PEARL();
        $pearl->setCustomName("§7[§8----====§7[ §ePERLY§r§7 ]§8====----§7]");
        $pearl->setLore([
            "",
            " §r§7Limit serwerowy §e".Settings::$PEARL_LIMIT,
            "§r§7Posiadane perly §e".$statManager->getStat(Settings::$STAT_ENDER_PEARLS),
        ]);

        $loreCreator = new LoreCreator($pearl->getCustomName(), $pearl->getLore());
        $loreCreator->alignLore();
        $pearl->setLore($loreCreator->getLore());

        $this->setItem(12, $pearl, true, true);

        // SNIEZKI

        $snowball = VanillaItems::SNOWBALL();
        $snowball->setCustomName("§7[§8----====§7[ §eSNIEZKI§r§7 ]§8====----§7]");
        $snowball->setLore([
            "",
            " §r§7Limit serwerowy §e".Settings::$SNOWBALL_LIMIT,
            "§r§7Posiadane sniezki §e".$statManager->getStat(Settings::$STAT_SNOWBALLS),
        ]);

        $loreCreator = new LoreCreator($snowball->getCustomName(), $snowball->getLore());
        $loreCreator->alignLore();
        $snowball->setLore($loreCreator->getLore());

        $this->setItem(14, $snowball, true, true);

        // STRZALY

        $arrow = VanillaItems::ARROW();
        $arrow->setCustomName("§7[§8----====§7[ §eSTRZALY§r§7 ]§8====----§7]");
        $arrow->setLore([
            "",
            " §r§7Limit serwerowy §e".Settings::$ARROW_LIMIT,
            "§r§7Posiadane strzaly §e".$statManager->getStat(Settings::$STAT_ARROWS),
        ]);

        $loreCreator = new LoreCreator($arrow->getCustomName(), $arrow->getLore());
        $loreCreator->alignLore();
        $arrow->setLore($loreCreator->getLore());

        $this->setItem(15, $arrow, true, true);

        // RZCUAK

        $throwableTnt = (new ThrownTNT())->__toItem();
        $throwableTnt->setCustomName("§7[§8----====§7[ §eRZUCAKI§r§7 ]§8====----§7]");
        $throwableTnt->setLore([
            "",
            " §r§7Limit serwerowy §e".Settings::$THROWABLE_TNT_LIMIT,
            "§r§7Posiadane rzucaki §e".$statManager->getStat(Settings::$STAT_THROWABLE_TNT),
        ]);

        $loreCreator = new LoreCreator($throwableTnt->getCustomName(), $throwableTnt->getLore());
        $loreCreator->alignLore();
        $throwableTnt->setLore($loreCreator->getLore());

        $this->setItem(16, $throwableTnt, true, true);

        // WSZYSTKO

        $netherStar = VanillaItems::NETHER_STAR()->setCustomName("§l§eWYPLAC LIMIT");
        $this->setItem(22, $netherStar, true, true);
    }

    public function onTransaction(Player $player, Item $sourceItem, Item $targetItem, int $slot) : bool {

        $user = Main::getInstance()->getUserManager()->getUser($player->getName());

        if(!$user) {
            return true;
        }

        $statManager = $user->getStatManager();

        $deposit = DepositUtil::getDepositData();

        foreach($deposit as $stat => $data) {
            if($sourceItem->getId() === $data["item"]->getId()) {
                if($statManager->getStat($stat) <= 0)
                    continue;

                $block = $player->getWorld()->getBlock($player->getPosition()->floor());
                $blockUp = $player->getWorld()->getBlock($player->getPosition()->floor());

                if($data["item"] instanceof EnderPearl && !$block instanceof Transparent && !$blockUp instanceof Transparent)
                    continue;

                $statManager->reduceStat($stat);
                $player->getInventory()->addItem($data["item"]);

                $this->setItems();
            }
        }

        if($sourceItem->getId() === ItemIds::NETHER_STAR) {
            foreach($player->getInventory()->getContents() as $item) {
                foreach($deposit as $stat => $data) {
                    if($item->equals($data["item"]))
                        $deposit[$stat]["count"] += $item->getCount();
                }
            }

            if(!empty($deposit)) {
                foreach($deposit as $stat => $data) {
                    if($data["count"] < $data["limit"]) {
                        $different = ($data["limit"] - $data["count"]);

                        if(($userStat = $statManager->getStat($stat)) < $different) {
                            if($userStat > 0)
                                $different = $userStat;
                            else
                                continue;
                        }

                        $block = $player->getWorld()->getBlock($player->getPosition()->floor());
                        $blockUp = $player->getWorld()->getBlock($player->getPosition()->floor());

                        if($data["item"] instanceof EnderPearl && !$block instanceof Transparent && !$blockUp instanceof Transparent)
                            continue;

                        $player->getInventory()->addItem($data["item"]->setCount($different));
                        $statManager->reduceStat($stat, $different);
                    }
                }
            }

            $this->setItems();
        }

        $this->unClickItem($player);
        return true;
    }
}