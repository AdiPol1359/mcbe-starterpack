<?php

namespace core\listener\events;

use core\listener\BaseListener;
use core\manager\managers\SkinManager;
use core\manager\managers\wing\WingsManager;
use core\util\utils\SkinUtil;
use pocketmine\entity\Skin;
use pocketmine\event\player\PlayerChangeSkinEvent;

class ChangeSkinListener extends BaseListener{

    public function checkSkin(PlayerChangeSkinEvent $e) : void{
        $player = $e->getPlayer();
        $skin = $e->getNewSkin();

        $newSkin = new Skin($skin->getSkinId(), SkinUtil::skinImageToBytes(SkinManager::getPlayerSkinImage($player->getName())), "", SkinManager::getDefaultGeometryName(), SkinManager::getDefaultGeometryData());

        $wings = WingsManager::getPlayerWings($player->getName());

        if($wings !== null)
            WingsManager::setWings($player, $wings);
        else {
            $player->setSkin($newSkin);
            $player->sendSkin();
        }

        SkinManager::setPlayerSkin($player, $newSkin);
    }
}