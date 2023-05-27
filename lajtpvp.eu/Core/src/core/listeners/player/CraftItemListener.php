<?php

declare(strict_types=1);

namespace core\listeners\player;

use core\utils\MessageUtil;
use core\utils\PermissionUtil;
use core\utils\Settings;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\Listener;
use pocketmine\item\ItemIds;

class CraftItemListener implements Listener {

    public function disableDiamondItems(CraftItemEvent $e) : void {

        if(!Settings::$DISABLE_DIAMOND_ITEMS)
            return;

        $items = [ItemIds::DIAMOND_SWORD, ItemIds::DIAMOND_HELMET, ItemIds::DIAMOND_CHESTPLATE, ItemIds::DIAMOND_LEGGINGS, ItemIds::DIAMOND_BOOTS];
        $player = $e->getPlayer();
        $outputs = $e->getOutputs();

        if(PermissionUtil::has($player, Settings::$PERMISSION_TAG."craft.diamond.items")) {
            return;
        }

        foreach($outputs as $output) {

            if(in_array($output->getId(), $items)) {
                $e->cancel();
                $player->sendMessage(MessageUtil::format("Diamentowe itemy sa wylaczone!"));
            }
        }
    }

    public function disableArmorStands(CraftItemEvent $e) : void {
        $outputs = $e->getOutputs();

        foreach($outputs as $output) {
            if($output->getId() === ItemIds::ARMOR_STAND || $output->getId() === ItemIds::SHIELD) {
                $e->cancel();
            }
        }
    }
}