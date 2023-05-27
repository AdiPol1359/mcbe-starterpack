<?php

declare(strict_types=1);

namespace core\listeners\entity;

use core\items\custom\ThrownTNT;
use core\Main;
use core\managers\ServerManager;
use core\utils\MessageUtil;
use core\utils\Settings;
use pocketmine\block\BlockLegacyIds;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;

class EntityExplodeListener implements Listener {

    /**
     * @param EntityExplodeEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function explodeOnTerrain(EntityExplodeEvent $e) : void {

        if($e->isCancelled())
            return;

        $blocks = $e->getBlockList();

        foreach($blocks as $num => $block) {

            if(in_array($block->getId(), Settings::$REGEN_BLOCK_IDS))
                continue;

            if(($guild = Main::getInstance()->getGuildManager()->getGuildFromPos($block->getPosition())) !== null)
                $guild->addRegenerationBlock($block);
        }
    }


    /**
     * @param EntityExplodeEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function heartTerrain(EntityExplodeEvent $e) : void {
        $blocks = $e->getBlockList();

        foreach($blocks as $num => $block) {

            if(($guild = Main::getInstance()->getGuildManager()->getGuildFromPos($block->getPosition())) !== null) {
                if($guild->isInHeart($block->getPosition()))
                    unset($blocks[$num]);
            }
        }

        $e->setBlockList($blocks);
    }

    /**
     * @param EntityExplodeEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function tntOnTerrain(EntityExplodeEvent $e) : void {

        $blocks = $e->getBlockList();
        $guild = Main::getInstance()->getGuildManager()->getGuildFromPos($e->getPosition());
        $entity = $e->getEntity();

        foreach($blocks as $key => $block) {

            if($block->getId() === BlockLegacyIds::TNT)
                continue;

            if(($guild = Main::getInstance()->getGuildManager()->getGuildFromPos($block->getPosition())) === null) {
                unset($blocks[$key]);
                continue;
            }

            if($guild->isTntEnabled())
                continue;

            if($guild->getConquerTime() > time() || !Main::getInstance()->getServerManager()->isSettingEnabled(ServerManager::TNT)) {
                unset($blocks[$key]);
            }
        }

        if(!empty($blocks)) {
            $guild?->explodeTnt();
        } else {
            $attacker = $entity->getOwningEntity();

            if($attacker && $attacker instanceof Player) {
                $attacker->sendMessage(MessageUtil::format("Tnt na tym terenie jest wylaczone!"));
                $attacker->getInventory()->addItem((new ThrownTNT())->__toItem());
                $e->cancel();
            }
        }

        $e->setBlockList($blocks);
    }

    /**
     * @param EntityExplodeEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function borderExplode(EntityExplodeEvent $e) : void {
        $blocks = $e->getBlockList();
        $border = Settings::$BORDER_DATA["border"];

        foreach($blocks as $key => $block) {
            $blockPos = $block->getPosition();

            if($blockPos->x >= $border || $blockPos->x <= -$border || $blockPos->z >= $border || $blockPos->z <= -$border)
                unset($blocks[$key]);
        }

        $e->setBlockList($blocks);
    }

    public function explodeOnSpawn(EntityExplodeEvent $e) : void {

        $blocks = $e->getBlockList();

        foreach($blocks as $key => $block) {
            if(($terrain = Main::getInstance()->getTerrainManager()->getPriorityTerrain($block->getPosition()))) {
                if($terrain->isSettingEnabled(Settings::$TERRAIN_BREAK_BLOCK))
                    unset($blocks[$key]);
            }
        }

        $e->setBlockList($blocks);
    }

    public function warCheck(EntityExplodeEvent $e) : void {

        $blocks = $e->getBlockList();

        foreach($blocks as $key => $block) {
            if(($guild = Main::getInstance()->getGuildManager()->getGuildFromPos($block->getPosition()))) {
                if(!($war = Main::getInstance()->getWarManager()->getWar($guild->getTag()))) {
                    unset($blocks[$key]);
                    continue;
                }

                if($war->getStartTime() > time() || $war->getEndTime() < time()) {
                    unset($blocks[$key]);
                }
            }
        }

        $e->setBlockList($blocks);
    }

    public function blockLimit(EntityExplodeEvent $e) : void {
        $blocks = $e->getBlockList();

        foreach($blocks as $key => $block) {
            if($block->getPosition()->getFloorY() > 50)
                unset($blocks[$key]);
        }

        $e->setBlockList($blocks);
    }
}