<?php

namespace core\manager\managers;

use core\manager\BaseManager;
use core\user\UserManager;
use pocketmine\entity\object\FireworksRocket;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\level\Level;
use pocketmine\level\particle\Particle;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\Player;

class ParticlesManager extends BaseManager {

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

    public static function spawnFirework(Player $player, Level $level, array $particle) : void{

        if(!UserManager::getUser($player->getName())->isSettingEnabled(SettingsManager::PARTICLES))
            return;

        $fw = ItemFactory::get(Item::FIREWORKS);

        foreach($particle as $row)
            $fw->addExplosion($row[0], $row[1]);


        $fw->setFlightDuration(0);

        $nbt = FireworksRocket::createBaseNBT($player->asVector3()->add(0, 2), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
        $entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);
        if ($entity instanceof FireworksRocket)
            $entity->spawnTo($player);
    }

    public static function spawnFireworkAt(Player $player, Level $level, array $particle, Position $position) : void{

        if(!UserManager::getUser($player->getName())->isSettingEnabled(SettingsManager::PARTICLES))
            return;

        $fw = ItemFactory::get(Item::FIREWORKS);

        foreach($particle as $row)
            $fw->addExplosion($row[0], $row[1]);


        $fw->setFlightDuration(0);

        $nbt = FireworksRocket::createBaseNBT($position, new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
        $entity = FireworksRocket::createEntity("FireworksRocket", $level, $nbt, $fw);
        if ($entity instanceof FireworksRocket)
            $entity->spawnTo($player);
    }

    public static function spawnParticle(Player $player, Particle $type) : void{
        if(!UserManager::getUser($player->getName())->isSettingEnabled(SettingsManager::PARTICLES))
            return;

        $player->getLevel()->addParticle($type);
    }

    public static function sendTotem(Player $player) : void{
        if(!UserManager::getUser($player->getName())->isSettingEnabled(SettingsManager::PARTICLES))
            return;
        $item = $player->getInventory()->getItemInHand();
        $player->getInventory()->setItemInHand(Item::get(Item::TOTEM));
        $player->broadcastEntityEvent(ActorEventPacket::CONSUME_TOTEM);
        $player->getInventory()->setItemInHand($item);
    }
}