<?php

namespace core\listener\events;

use core\entity\entities\custom\CaveSpawn;
use core\entity\entities\mobs\Villager;
use core\listener\BaseListener;
use core\manager\managers\bossbar\BossbarManager;
use core\manager\managers\quest\QuestManager;
use core\user\UserManager;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\inventory\CraftItemEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\Player;

class QuestEventListener extends BaseListener{

    /**
     * @param BlockBreakEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function BreakBlock(BlockBreakEvent $e) : void {

        if($e->isCancelled())
            return;

        $player = $e->getPlayer();
        $block = $e->getBlock();

        $userManager = UserManager::getUser($player->getName());
        if(!$userManager->isSelectedQuest())
            return;

        if($userManager->hasMadeQuest())
            return;

        $quest = $userManager->getSelectedQuest();
        if($quest->getType() === "BREAK_BLOCK")
            if($block->getId() == $quest->getItemId() && $block->getDamage() == $quest->getItemDamage())
                $userManager->addToStatus();

        if(BossbarManager::getBossbar($player) !== null)
            QuestManager::update($player);
    }

    /**
     * @param CraftItemEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */

    public function CraftItem(CraftItemEvent $e) : void {

        if($e->isCancelled())
            return;

        $player = $e->getPlayer();

        $userManager = UserManager::getUser($player->getName());
        if(!$userManager->isSelectedQuest())
            return;

        if($userManager->hasMadeQuest())
            return;

        $quest = $userManager->getSelectedQuest();
        if($quest->getType() === "MAKE_ITEM") {
            foreach($e->getOutputs() as $output) {
                if($output->getId() == $quest->getItemId() && $output->getDamage() == $quest->getItemDamage())
                    for($i = 0; $i < $output->getCount(); $i++)
                        $userManager->addToStatus();
            }
        }
        if(BossbarManager::getBossbar($player) != null)
            QuestManager::update($player);
    }

    /**
     * @param PlayerMoveEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function WalkQuest(PlayerMoveEvent $e) : void{
        if($e->isCancelled())
            return;

        if($e->getFrom()->floor()->equals($e->getTo()->floor()))
            return;

        $player = $e->getPlayer();

        $userManager = UserManager::getUser($player->getName());
        if(!$userManager->isSelectedQuest())
            return;

        if($userManager->hasMadeQuest())
            return;

        $quest = $userManager->getSelectedQuest();
        if($quest->getType() === "WALK")
            $userManager->addToStatus();

        if(BossbarManager::getBossbar($player) != null)
            QuestManager::update($player);
    }

    /**
     * @param EntityDamageEvent $e
     * @priority HIGHEST
     * @ignoreCancelled true
     */
    public function KillQuest(EntityDamageEvent $e) : void{

        if(!$e instanceof EntityDamageByEntityEvent)
            return;

        if($e->isCancelled())
            return;

        $entity = $e->getEntity();

        if($entity instanceof Villager || $entity instanceof CaveSpawn)
            return;

        if ($e->getFinalDamage() >= $entity->getHealth()) {
            $damager = $e->getDamager();

            if(!$damager instanceof Player)
                return;

            $userManager = UserManager::getUser($damager->getName());
            if(!$userManager->isSelectedQuest())
                return;

            if($userManager->hasMadeQuest())
                return;

            $quest = $userManager->getSelectedQuest();
            if($quest->getType() === "KILL")
                $userManager->addToStatus();

            if(BossbarManager::getBossbar($damager) != null)
                QuestManager::update($damager);
        }
    }
}