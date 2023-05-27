<?php

namespace core\entities\custom;

use core\inventories\fakeinventories\VillagerShopInventory;
use core\Main;
use core\managers\villager\VillagerShop;
use core\utils\MessageUtil;
use core\utils\Settings;
use core\utils\SkinUtil;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\entity\Skin;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;

class VillagerShopEntity extends Human {

    private ?VillagerShop $villager;
    private ?Player $closestPlayer = null;
    protected $gravity = 0;

    public function __construct(Location $location, Skin $skin, CompoundTag $nbt) {
        parent::__construct($location, $skin, $nbt);

        if(!$nbt->getTag("villagerId")) {
            $this->close();
            return;
        }

        $id = $nbt->getInt("villagerId");

        if(Main::getInstance()->getVillagerShopManager() === null) {
            $this->close();
            return;
        }

        if(($villager = Main::getInstance()->getVillagerShopManager()->getVillager($id)) === null) {
            $this->close();
            return;
        }

        $this->villager = $villager;
        $this->setNameTag($villager->getName());
    }

    protected function initEntity(CompoundTag $nbt) : void {
        $skin = SkinUtil::getSkinFromPath(Main::getInstance()->getDataFolder() . "/default/villager.png");

        $this->setMaxHealth(1);
        $this->setSkin(new Skin($this->getSkin()->getSkinId(), $skin));

        parent::initEntity($nbt);
    }

    public function onFirstInteract(Player $player) : void {
        if($this->villager)
            (new VillagerShopInventory($this->villager))->openFor([$player]);
    }

    public function attack(EntityDamageEvent $source) : void {
        if(!$this->villager)
            return;

        if(!$source instanceof EntityDamageByEntityEvent)
            return;

        $damager = $source->getDamager();

        if($damager instanceof Player) {
            $user = Main::getInstance()->getUserManager()->getUser($damager->getName());
            if(!$user)
                return;

            if($user->hasLastData(Settings::$CHOOSE_SHOP_VILLAGER)) {
                $damager->sendMessage(MessageUtil::format("Id tego villaera wynosi §e" . $this->villager->getId()));
                $user->removeLastData(Settings::$CHOOSE_SHOP_VILLAGER);
            }
        }
    }

    public function setSkin(Skin $skin) : void {
        parent::setSkin(new Skin($skin->getSkinId(), $skin->getSkinData(), '', 'geometry.defaultGeometry', file_get_contents(Main::getInstance()->getDataFolder() . "default/defaultGeometry.json")));
    }

    public function entityBaseTick(int $tickDiff = 1) : bool {

        if(!$this->closestPlayer)
            $this->updateClosestPlayer();
        else {
            $ePos = $this->getPosition();
            $pPos = $this->closestPlayer->getPosition();

            $x = $pPos->getX() - $ePos->getX();
            $y = $pPos->getY() - $ePos->getY();
            $z = $pPos->getZ() - $ePos->getZ();

            $len = sqrt($x * $x + $y + $z * $z);

            if($len == 0)
                return parent::entityBaseTick($tickDiff);

            $y = $y / $len;

            $pitch = -(asin($y) * 180 / M_PI);

            $yaw = -atan2($x, $z) * (180 / M_PI);

            if(!($pitch < 89) && !($pitch > -89))
                return parent::entityBaseTick($tickDiff);

            $this->setRotation($yaw, $pitch);
            $this->move(0, 0, 0);
        }

        $this->updateClosestPlayer();
        return parent::entityBaseTick($tickDiff);
    }

    private function updateClosestPlayer() : void {
        $closestPlayer = null;

        foreach($this->getWorld()->getPlayers() as $player) {
            $position = $this->getPosition();

            if($player->getPosition()->distance($position) >= 7)
                continue;

            if(!$closestPlayer)
                $closestPlayer = $player;

            if($closestPlayer->getPosition()->distance($position) > $player->getPosition()->distance($position))
                $closestPlayer = $player;
        }

        $this->closestPlayer = $closestPlayer;
    }

    public function knockBack(float $x, float $z, float $force = 0.4, ?float $verticalLimit = 0.4) : void {
    }

    public function setOnFire(int $seconds) : void {
    }

    public function setMotion(Vector3 $motion) : bool {
        return false;
    }

    public function getVillager() : ?VillagerShop {
        return $this->villager;
    }
}