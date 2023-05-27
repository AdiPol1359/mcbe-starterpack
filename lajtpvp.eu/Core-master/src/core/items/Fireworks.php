<?php

declare(strict_types=1);

namespace core\items;

use core\entities\object\FireworksRocket;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;

class Fireworks extends Item {

    /** @var float */
    public const BOOST_POWER = 1.25;

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

    public function __construct(int $meta = 0){
        parent::__construct(new ItemIdentifier(ItemIds::FIREWORKS, $meta), "Fireworks");
    }

    public function getFlightDuration() : int{
        return $this->getExplosionsTag()->getByte("Flight", 1);
    }

    public function setFlightDuration(int $duration) : void{
        $tag = $this->getExplosionsTag();
        $tag->setByte("Flight", $duration);
        $this->setNamedTag($tag);
    }

    protected function getExplosionsTag() : CompoundTag{
        $compoundTag = CompoundTag::create();
        $compoundTag->setTag("Fireworks", CompoundTag::create());
        return $this->getNamedTag()->getCompoundTag("Fireworks") ?? $compoundTag;
    }

    public function addExplosion(int $type, string $color, string $fade = "", int $flicker = 0, int $trail = 0) : void{
        $explosion = new CompoundTag();
        $explosion->setByte("FireworkType", $type);
        $explosion->setByteArray("FireworkColor", $color);
        $explosion->setByteArray("FireworkFade", $fade);
        $explosion->setByte("FireworkFlicker", $flicker);
        $explosion->setByte("FireworkTrail", $trail);

        $tag = $this->getExplosionsTag();
        $explosions = $tag->getListTag("Explosions") ?? new ListTag();
        $explosions->push($explosion);
        $tag->setTag("Explosions", $explosions);
        $this->setNamedTag($tag);
    }

    public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult {
        $nbt = new FireworksRocket($player->getLocation(),$this, 40);

        if($nbt instanceof Entity){
            $this->pop();
            $nbt->spawnToAll();
            return ItemUseResult::SUCCESS();
        }

        return parent::onClickAir($player, $directionVector);
    }

    public function getRandomizedFlightDuration() : int{
        return 0;
    }
}