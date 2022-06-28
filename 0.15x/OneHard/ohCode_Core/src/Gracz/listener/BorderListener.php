<?php

namespace Gracz\listener;

use Gracz\Main;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\Player;

class BorderListener implements Listener {

    /** @var EBorder */
    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onEntityTeleportEvent(EntityTeleportEvent $event) {

        if(!$event->getEntity() instanceof Player) return;

        /** @var Player $player */
        $player = $event->getEntity();

        if(in_array($player->getName(), $this->plugin->teleports)) {

            # Remove the entry if it is present.
            if(($key = array_search($player->getName(), $this->plugin->teleports)) !== false) {
                unset($this->plugin->teleports[$key]);
            }
            return;

        }

        if($this->plugin->border->insideBorder($player->getX(), $player->getZ())) return;

        $player->sendMessage($this->plugin->msgTeleport);
        $event->setCancelled(true);

    }

    public function onBlockPlace(BlockPlaceEvent $event) {

        if($this->plugin->border->insideBorder($event->getBlock()->getX(), $event->getBlock()->getZ())) return;

        $event->getPlayer()->sendMessage($this->plugin->msgOutOfReach);

        $event->setCancelled(true);

    }

}
