<?php
namespace EssentialsPE\EventHandlers;

use EssentialsPE\BaseFiles\BaseEventHandler;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityExplodeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\server\ServerCommandEvent;
use pocketmine\math\Vector3;

class OtherEvents extends BaseEventHandler{
    /**
     * @param ServerCommandEvent $event
     */
    public function onServerCommand(ServerCommandEvent $event){
        $command = $this->getPlugin()->colorMessage($event->getCommand());
        if($command === false){
            $event->setCancelled(true);
        }
        $event->setCommand($command);
    }

    /**
     * @param EntityExplodeEvent $event
     */
    public function onTNTExplode(EntityExplodeEvent $event){
        if($event->getEntity()->namedtag->getName() === "EssNuke"){
            $event->setBlockList([]);
        }
    }

    /**
     * @param PlayerInteractEvent $event
     *
     * @priority HIGH
     */
    public function onBlockTap(PlayerInteractEvent $event){// PowerTool
        if($this->getPlugin()->executePowerTool($event->getPlayer(), $event->getItem())){
            $event->setCancelled(true);
        }
    }

    /**
     * @param BlockPlaceEvent $event
     *
     * @priority HIGH
     */
    public function onBlockPlace(BlockPlaceEvent $event){
        // PowerTool
        if($this->getPlugin()->executePowerTool($event->getPlayer(), $event->getItem())){
            $event->setCancelled(true);
        }

        // Unlimited block placing
        elseif($this->getPlugin()->isUnlimitedEnabled($event->getPlayer())){
            $event->setCancelled(true);
            $pos = new Vector3($event->getBlockReplaced()->getX(), $event->getBlockReplaced()->getY(), $event->getBlockReplaced()->getZ());
            $event->getPlayer()->getLevel()->setBlock($pos, $event->getBlock(), true);
        }
    }
}