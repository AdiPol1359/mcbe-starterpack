<?php

namespace core\listener\events;

use core\listener\BaseListener;
use core\manager\managers\SettingsManager;
use core\user\UserManager;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\item\Armor;
use pocketmine\item\Bow;
use pocketmine\item\Sword;
use pocketmine\item\Tool;

class ItemHeldListener extends BaseListener{
    public function setItemStatus(PlayerItemHeldEvent $e) : void {
        $player = $e->getPlayer();
        $item = $e->getItem();

        if($item->getId() == 0) {
            $player->setXpProgress(0);
            $player->setXpLevel(0);
            return;
        }

        if(!UserManager::getUser($player->getName())->isSettingEnabled(SettingsManager::ITEM_STATUS)) {
            $player->setXpProgress(0);
            $player->setXpLevel(0);
            return;
        }

        if(!$item instanceof Sword && !$item instanceof Tool && !$item instanceof Bow && !$item instanceof Armor) {
            $player->setXpProgress(0);
            $player->setXpLevel(0);
            return;
        }

        $damage = $item->getDamage();
        $max_dmg = $item->getMaxDurability();
        $percentage = ($damage / $max_dmg) * 100;

        if($max_dmg < $damage)
            return;

        $player->setXpLevel($max_dmg - $damage);
        $player->setXpProgress(1.0 - $percentage / 100);
    }
}