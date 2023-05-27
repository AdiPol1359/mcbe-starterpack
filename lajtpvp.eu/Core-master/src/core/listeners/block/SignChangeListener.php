<?php

declare(strict_types=1);

namespace core\listeners\block;

use core\Main;
use core\utils\MessageUtil;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\utils\SignText;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;

class SignChangeListener implements Listener {

    public function lockChest(SignChangeEvent $e) : void {
        $player = $e->getPlayer();
        $nick = $player->getName();
        $block = $e->getBlock();
        $firstLine = $e->getNewText()->getLine(0);

        if($block->getId() !== BlockLegacyIds::WALL_SIGN)
            return;

        $resultSide = $block->getMeta();

        if($resultSide >= 0 and $resultSide <= 5){
            $resultSide = $resultSide ^ 0x01;
        }

        if(($block->getSide($resultSide) != BlockLegacyIds::CHEST)) {
            return;
        }

        if(strtolower($firstLine) === "[lock]") {
            $chestLocker = Main::getInstance()->getChestLockerManager();

            $e->setNewText(new SignText([
                "[§e§lLOCK§r]",
                "SKRZYNIA GRACZA",
                strlen($nick) > 14 ? "§e".substr($nick, 0, 16) . "..." : "§e".$nick,
                ""
            ]));

            if($chestLocker->getLockLimit($player) <= count($chestLocker->getPlayerLockedChests($player->getName()))) {
                $player->sendMessage(MessageUtil::format("Osiagnales limit zablokowanych skrzynek!"));
                return;
            }

            if(!$chestLocker->getLocker($block->getPosition())) {
                $chestLocker->createChestLocker($nick, $block->getMeta(), $block->getPosition());
            }
        }
    }
}