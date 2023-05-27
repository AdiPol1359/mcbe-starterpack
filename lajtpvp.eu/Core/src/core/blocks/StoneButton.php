<?php

declare(strict_types=1);

namespace core\blocks;

use core\utils\RandomUtil;
use core\utils\Settings;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\StoneButton as PMStoneButton;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;

class StoneButton extends PMStoneButton {

    public function __construct() {
        parent::__construct(
            new BlockIdentifier(BlockLegacyIds::STONE_BUTTON, 0),
            "Stone Button",
            new BlockBreakInfo(0.5, BlockToolType::PICKAXE)
        );
    }
    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null) : bool {
        if(!$player)
            return false;

        foreach($this->getHorizontalSides() as $block) {
            foreach(Settings::$RANDOM_TP as $key => $randomTpData) {
                if($block->asVector3()->equals($randomTpData["position"])) {
                    $mainPosition = clone $randomTpData["position"];

                    switch($randomTpData["type"]) {

                        case Settings::TYPE_SELF_TP:
                            RandomUtil::randomTeleport([$player]);
                            break;

                        case Settings::TYPE_GROUP_TP:

                            $players = [];
                            $isInRange = false;

                            foreach(Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                                if(floor(sqrt(pow($mainPosition->x - $onlinePlayer->getPosition()->getFloorX(), 2) + pow($mainPosition->z - $onlinePlayer->getPosition()->getFloorZ(), 2))) <= $randomTpData["distance"]) {
                                    $players[] = $onlinePlayer;

                                    if($onlinePlayer->getName() === $player->getName())
                                        $isInRange = true;
                                }
                            }

                            if(count($players) > 1) {
                                if($isInRange)
                                    RandomUtil::randomTeleport($players);
                            }

                            break;

                        case Settings::TYPE_GROUP_1V1_TP:

                            $players = [];
                            $isInRange = false;

                            foreach(Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                                if($onlinePlayer->getName() === $player->getName())
                                    $isInRange = true;

                                if(count($players) >= 2)
                                    break;

                                if(floor(sqrt(pow($mainPosition->x - $onlinePlayer->getPosition()->getFloorX(), 2) + pow($mainPosition->z - $onlinePlayer->getPosition()->getFloorZ(), 2))) <= $randomTpData["distance"]) {
                                    if($onlinePlayer->canInteract($mainPosition, $onlinePlayer->isCreative() ? 13 : 7))
                                        $players[] = $onlinePlayer;
                                }
                            }

                            if(count($players) > 1) {
                                if($isInRange)
                                    RandomUtil::randomTeleport($players);
                            }
                            break;
                    }
                }
            }
        }

        return parent::onInteract($item, $face, $clickVector, $player);
    }
}