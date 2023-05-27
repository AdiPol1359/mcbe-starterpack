<?php

declare(strict_types=1);

namespace core\listeners\inventory;

use core\items\custom\ThrownTNT;
use core\Main;
use core\utils\DepositUtil;
use core\utils\MessageUtil;
use core\utils\Settings;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\item\ItemIds;
use pocketmine\player\GameMode;

class ItemHeldListener implements Listener {

    public function guildTerrain(PlayerItemHeldEvent $e) : void {
        $player = $e->getPlayer();

        if(($guild = Main::getInstance()->getGuildManager()->getGuildFromPos($player->getPosition())) === null) {
            if($player->isAdventure() && !$player->isSpectator())
                $player->setGamemode(GameMode::SURVIVAL());
        }
    }

    public function depositUpdate(PlayerItemHeldEvent $e) : void {
        $player = $e->getPlayer();
        $item = $e->getItem();

        if($item->getId() !== ItemIds::ENCHANTED_GOLDEN_APPLE && $item->getId() !== ItemIds::GOLDEN_APPLE && $item->getId() !== ItemIds::ENDER_PEARL && $item->getId() !== ItemIds::SNOWBALL && $item->getId() !== ItemIds::ARROW && !$item->equals(new ThrownTNT()))
            return;

        $user = Main::getInstance()->getUserManager()->getUser($player->getName());

        if(!$user)
            return;

        $terrains = Main::getInstance()->getTerrainManager()->getTerrainsFromPos($player->getPosition());

        foreach($terrains as $terrain) {
            if($terrain->getName() === Settings::$SPAWN_TERRAIN)
                return;
        }

        $deposit = DepositUtil::getDepositData();

        foreach($player->getInventory()->getContents(false) as $item) {
            foreach($deposit as $stat => $data) {
                if($item->equals($data["item"], false, false))
                    $deposit[$stat]["count"] += $item->getCount();
            }
        }

        if(!empty($deposit)) {
            foreach($deposit as $stat => $data) {
                if($data["count"] > $data["limit"]) {
                    $different = ($data["count"] - $data["limit"]);
                    $player->getInventory()->removeItem($data["item"]->setCount($different));
                    $user->getStatManager()->addStat($stat, $different);

                    $player->sendMessage(MessageUtil::format("§cTwoj nadmiar zostal przeniesiony do depozytu §8(§c".$data["normalName"]." x".$different."§8)"));
                }
            }
        }
    }
}