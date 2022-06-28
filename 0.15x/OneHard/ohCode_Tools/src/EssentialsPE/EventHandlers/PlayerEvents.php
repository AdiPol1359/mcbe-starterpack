<?php

namespace EssentialsPE\EventHandlers;

use EssentialsPE\BaseFiles\BaseEventHandler;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\player\PlayerBedEnterEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\TextContainer;
use pocketmine\event\TranslationContainer;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class PlayerEvents extends BaseEventHandler{
    /**
     * @param PlayerPreLoginEvent $event
     *
     * @priority MONITOR
     * @ignoreCancelled true
     */
    public function onPlayerPreLogin(PlayerPreLoginEvent $event){
        // Ban remove:
        if($event->getPlayer()->isBanned() && $event->getPlayer()->hasPermission("essentials.ban.exempt")){
            $event->getPlayer()->setBanned(false);
        }
        // Session configure:
        $this->getPlugin()->createSession($event->getPlayer());
    }

    /**
     * @param PlayerJoinEvent $event
     */
    public function onPlayerJoin(PlayerJoinEvent $event){
        // Nick and NameTag set:
        $message = $event->getJoinMessage();
        if($message instanceof TranslationContainer){
            foreach($message->getParameters() as $i => $m){
                $message->setParameter($i, str_replace($event->getPlayer()->getName(), $event->getPlayer()->getDisplayName(), $m));
            }
        }elseif($message instanceof TextContainer){
            $message->setText(str_replace($event->getPlayer()->getName(), $event->getPlayer()->getDisplayName(), $message->getText()));
        }else{
            $message = str_replace($event->getPlayer()->getName(), $event->getPlayer()->getDisplayName(), $message);
        }
        $event->setJoinMessage($message);

        // Hide vanished players with "noPacket"
        foreach($event->getPlayer()->getServer()->getOnlinePlayers() as $p){
            if($this->getPlugin()->isVanished($p) && $this->getPlugin()->hasNoPacket($p)){
                $event->getPlayer()->hidePlayer($p);
            }
        }
        $i = $this->getPlugin()->getMutedUntil($event->getPlayer());
        if($i instanceof \DateTime && $event->getPlayer()->hasPermission("essentials.mute.notify")){
            $event->getPlayer()->sendMessage(TextFormat::YELLOW . "Remember that you're muted until " . TextFormat::AQUA . $i->format("l, F j, Y") . TextFormat::YELLOW . " at " . TextFormat::AQUA . $i->format("h:ia"));
        }
        //$this->getPlugin()->setPlayerBalance($event->getPlayer(), $this->getPlugin()->getDefaultBalance()); TODO
    }

    /**
     * @param PlayerQuitEvent $event
     */
    public function onPlayerQuit(PlayerQuitEvent $event){
        // Quit message (nick):
        $message = $event->getQuitMessage();
        if($message instanceof TranslationContainer){
            foreach($message->getParameters() as $i => $m){
                $message->setParameter($i, str_replace($event->getPlayer()->getName(), $event->getPlayer()->getDisplayName(), $m));
            }
        }elseif($message instanceof TextContainer){
            $message->setText(str_replace($event->getPlayer()->getName(), $event->getPlayer()->getDisplayName(), $message->getText()));
        }else{
            $message = str_replace($event->getPlayer()->getName(), $event->getPlayer()->getDisplayName(), $message);
        }
        $event->setQuitMessage($message);

        // Session destroy:
        if($this->getPlugin()->sessionExists($event->getPlayer())){
            $this->getPlugin()->removeSession($event->getPlayer());
        }
    }

    /**
     * @param PlayerChatEvent $event
     */
    public function onPlayerChat(PlayerChatEvent $event){
        if($this->getPlugin()->isMuted($event->getPlayer())){
            if($event->getPlayer()->hasPermission("essentials.mute.exempt")){
                $this->getPlugin()->setMute($event->getPlayer(), false, null, false);
            }elseif(($t = $this->getPlugin()->getMutedUntil($event->getPlayer())) === null){
                $event->setCancelled(true);
            }else{
                $t2 = new \DateTime();
                if($t < $t2){
                    $this->getPlugin()->setMute($event->getPlayer(), false, null, false);
                }else{
                    $event->setCancelled(true);
                }
            }
        }elseif($this->getPlugin()->isAFK($event->getPlayer())){
            $this->getPlugin()->setAFKMode($event->getPlayer(), false, true);
        }
    }

    /**
     * @param PlayerCommandPreprocessEvent $event
     */
    public function onPlayerCommand(PlayerCommandPreprocessEvent $event){
        $command = $this->getPlugin()->colorMessage($event->getMessage(), $event->getPlayer());
        if($command === false){
            $event->setCancelled(true);
        }
        $event->setMessage($command);
    }

    /**
     * @param PlayerMoveEvent $event
     */
    public function onPlayerMove(PlayerMoveEvent $event){
        $entity = $event->getPlayer();
        if($this->getPlugin()->isAFK($entity)){
            $this->getPlugin()->setAFKMode($entity, false, true);
        }

        $this->getPlugin()->setLastPlayerMovement($entity, time());
    }

    /**
     * @param EntityTeleportEvent $event
     */
    public function onEntityTeleport(EntityTeleportEvent $event){
        $entity = $event->getEntity();
        if($entity instanceof Player){
            $this->getPlugin()->setPlayerLastPosition($entity, $entity->getLocation());
        }
    }

    /**
     * @param EntityLevelChangeEvent $event
     *
     * @priority MONITOR
     */
    public function onEntityLevelChange(EntityLevelChangeEvent $event){
        $entity = $event->getEntity();
        if($entity instanceof Player){
            $this->getPlugin()->switchLevelVanish($entity, $event->getOrigin(), $event->getTarget());
        }
    }

    /**
     * @param PlayerBedEnterEvent $event
     */
    public function onPlayerSleep(PlayerBedEnterEvent $event){
        if($event->getPlayer()->hasPermission("essentials.home.bed")){
            $this->getPlugin()->setHome($event->getPlayer(), "bed", $event->getPlayer()->getPosition());
        }
    }

    /**
     * @param EntityDamageEvent $event
     *
     * @priority HIGH
     */
    public function onEntityDamageByEntity(EntityDamageEvent $event){
        $victim = $event->getEntity();
        if($victim instanceof Player){
            if($this->getPlugin()->isGod($victim) || ($this->getPlugin()->isAFK($victim)) && $this->getPlugin()->getConfig()->getNested("afk.safe")){
                $event->setCancelled(true);
            }

            if($event instanceof EntityDamageByEntityEvent){
                $issuer = $event->getDamager();
                if($issuer instanceof Player){
                    if(!($s = $this->getPlugin()->isPvPEnabled($issuer)) || !$this->getPlugin()->isPvPEnabled($victim)){
                        $issuer->sendMessage(TextFormat::RED . (!$s ? "You have" : $victim->getDisplayName() . " has") . " PvP disabled!");
                        $event->setCancelled(true);
                    }

                    if($this->getPlugin()->isGod($issuer) && !$issuer->hasPermission("essentials.god.pvp")){
                        $event->setCancelled(true);
                    }

                    if($this->getPlugin()->isVanished($issuer) && !$issuer->hasPermission("essentials.vanish.pvp")){
                        $event->setCancelled(true);
                    }
                }
            }
        }
    }

    /**
     * @param PlayerDeathEvent $event
     */
    public function onPlayerDeath(PlayerDeathEvent $event){
        if($event->getEntity()->hasPermission("essentials.back.ondeath")){
            $this->getPlugin()->setPlayerLastPosition($event->getEntity(), $event->getEntity()->getLocation());
        }else{
            $this->getPlugin()->removePlayerLastPosition($event->getEntity());
        }
    }
}
