<?php

declare(strict_types=1);

namespace core\utils;

use core\entities\object\FireworksRocket;
use core\items\Fireworks;
use pocketmine\entity\animation\TotemUseAnimation;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\world\particle\Particle;
use pocketmine\world\Position;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\Server;

final class ParticleUtil {

    public const TYPE_SMALL_SPHERE = 0;
    public const TYPE_HUGE_SPHERE = 1;
    public const TYPE_STAR = 2;
    public const TYPE_CREEPER_HEAD = 3;
    public const TYPE_BURST = 4;
    
    public const COLOR_BLACK = "\x00";
    public const COLOR_RED = "\x01";
    public const COLOR_DARK_GREEN = "\x02";
    public const COLOR_BROWN = "\x03";
    public const COLOR_BLUE = "\x04";
    public const COLOR_DARK_PURPLE = "\x05";
    public const COLOR_DARK_AQUA = "\x06";
    public const COLOR_GRAY = "\x07";
    public const COLOR_DARK_GRAY = "\x08";
    public const COLOR_PINK = "\x09";
    public const COLOR_GREEN = "\x0a";
    public const COLOR_YELLOW = "\x0b";
    public const COLOR_LIGHT_AQUA = "\x0c";
    public const COLOR_DARK_PINK = "\x0d";
    public const COLOR_GOLD = "\x0e";
    public const COLOR_WHITE = "\x0f";

    public function __construct() {}

    public static function spawnFirework(Player $player, array $particle) : void{
        $fw = ItemFactory::getInstance()->get(ItemIds::FIREWORKS);

        if(!$fw instanceof Fireworks) {
            return;
        }

        foreach($particle as $row) {
            $fw->addExplosion($row[0], $row[1]);
        }

        $fw->setFlightDuration(0);
        $nbt = new FireworksRocket($player->getLocation(),$fw, 0);

        if($nbt instanceof Entity){
            $nbt->spawnToAll();
        }
    }

    public static function spawnFireworkAt(array $players, array $particle, Position $position) : void{
        $fw = ItemFactory::getInstance()->get(ItemIds::FIREWORKS);

        if(!$fw instanceof Fireworks) {
            return;
        }

        foreach($particle as $row) {
            $fw->addExplosion($row[0], $row[1]);
        }

        $fw->setFlightDuration(0);
        $nbt = new FireworksRocket(new Location($position->x, $position->y, $position->z, $position->getWorld(), 0, 0),$fw, 0);

        if($nbt instanceof Entity){
            if ($nbt instanceof FireworksRocket) {
                if(empty($player)) {
                    $nbt->spawnToAll();
                } else {
                    foreach($players as $player)
                        $nbt->spawnTo($player);
                }
            }
        }
    }

    public static function spawnParticle(array $players, Particle $type) : void{

        if(empty($players)) {
            BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($type) : void {
                if($onlinePlayer instanceof Player) {
                    $onlinePlayer->getWorld()->addParticle($onlinePlayer->getPosition(), $type);
                }
            });
            return;
        }

        foreach($players as $player) {
            Server::getInstance()->getWorldManager()->getDefaultWorld()->addParticle($player->getPosition(), $type);
        }
    }

    public static function sendTotem(Player $player) : void{
        $item = $player->getInventory()->getItemInHand();
        $player->getInventory()->setItemInHand(ItemFactory::getInstance()->get(ItemIds::TOTEM));
        $player->broadcastAnimation(new TotemUseAnimation($player));
        $player->getInventory()->setItemInHand($item);
    }

    public static function broadcastEntityEvent(int $id, int $eventId, ?int $eventData = null, ?array $players = null) : void{
        $pk = new ActorEventPacket();
        $pk->entityRuntimeId = $id;
        $pk->event = $eventId;
        $pk->data = $eventData ?? 0;

        Server::getInstance()->broadcastPackets($players ?? Server::getInstance()->getOnlinePlayers(), [$pk]);
    }
}