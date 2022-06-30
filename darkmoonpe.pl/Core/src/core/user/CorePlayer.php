<?php

namespace core\user;

use core\Main;
use core\manager\managers\LogManager;
use core\manager\managers\MagicCaseManager;
use core\manager\managers\SettingsManager;
use core\manager\managers\UpgradeManager;
use core\util\utils\ConfigUtil;
use core\util\utils\InventoryUtil;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\Player;

class CorePlayer extends Player{

    public function respawn() : void{

        parent::respawn();

        $user = UserManager::getUser($this->getName());

        if(!$user)
            return;

        if($user->isSettingEnabled(SettingsManager::NIGHT_VISION)){
            $effect = new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION));
            $effect->setVisible(false);
            $effect->setDuration(INT32_MAX);
            $this->addEffect($effect);
        }
    }

    public function save() {

        parent::save();

        if(MagicCaseManager::openingMagicCase($this)) {
            InventoryUtil::addItem(($resultItem = MagicCaseManager::getRandomItem()), $this);
            LogManager::sendLog($this, "OpenResultQuit: " . $resultItem->getId() . ":" . $resultItem->getDamage() . ":" . $resultItem->getCount(), LogManager::MAGIC_CASE);
        }

        if(UpgradeManager::openingUpgradeDraw($this)) {
            InventoryUtil::addItem(($resultItem = UpgradeManager::getUpgradeDrawInventory($this)::getRandomItem()), $this);
            LogManager::sendLog($this, "UpgradeResultQuit: " . $resultItem->getId() . ":" . $resultItem->getDamage() . ":" . $resultItem->getCount(), LogManager::BLACK_SMITH);
        }
    }

    public function spawnTo(Player $player) : void {
        if(!in_array($this->getName(), Main::$vanish) || $player->hasPermission(ConfigUtil::PERMISSION_TAG . "vanish.see"))
            parent::spawnTo($player);
    }

    public function handleMovePlayer(MovePlayerPacket $packet) : bool {
        $rawPos = $packet->position;

        foreach([$rawPos->x, $rawPos->y, $rawPos->z, $packet->yaw, $packet->headYaw, $packet->pitch] as $float){
            if(is_infinite($float) || is_nan($float)){
                $this->server->getLogger()->debug("Invalid movement from " . $this->getName() . ", contains NAN/INF components");
                return false;
            }
        }

        $newPos = $rawPos->round(4)->subtract(0, $this->baseOffset);

        if((!$this->isAlive() or !$this->spawned) and $newPos->distanceSquared($this) > 0.01){
            $this->sendPosition($this, null, null, MovePlayerPacket::MODE_RESET);
            $this->server->getLogger()->debug("Reverted movement of " . $this->getName() . " due to not alive or not spawned, received " . $newPos . ", locked at " . $this->asVector3());
        }else{

            $this->forceMoveSync = null;

            $packet->yaw = fmod($packet->yaw, 360);
            $packet->pitch = fmod($packet->pitch, 360);

            if($packet->yaw < 0){
                $packet->yaw += 360;
            }

            $this->setRotation($packet->yaw, $packet->pitch);
            $this->handleMovement($newPos);
        }

        return true;
    }

    public function removeEffect(int $effectId) : void {

        if($effectId === Effect::NIGHT_VISION) {
            $user = UserManager::getUser($this->getName());

            if($user && $user->isSettingEnabled(SettingsManager::NIGHT_VISION))
                return;
        }

        parent::removeEffect($effectId);
    }
}