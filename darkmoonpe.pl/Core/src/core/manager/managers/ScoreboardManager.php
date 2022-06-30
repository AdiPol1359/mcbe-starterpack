<?php

namespace core\manager\managers;

use core\Main;
use core\manager\BaseManager;
use core\user\UserManager;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\Player;

class ScoreboardManager extends BaseManager {

    public static function addScoreboard(Player $player, string $displayName) {

        if(isset(Main::$sb[$player->getName()]))
            self::removeScoreboard($player);

        $pk = new SetDisplayObjectivePacket();
        $pk->displaySlot = "sidebar";
        $pk->objectiveName = "Core_sidebar";
        $pk->displayName = $displayName;
        $pk->criteriaName = "dummy";
        $pk->sortOrder = 0;

        $player->sendDataPacket($pk);

        Main::$sb[$player->getName()] = true;
    }

    public static function removeScoreboard(Player $player) {
        $pk = new RemoveObjectivePacket();
        $pk->objectiveName = "Core_sidebar";
        $player->sendDataPacket($pk);
    }

    public static function unsetScoreboard(Player $player){
        unset(Main::$sb[$player->getName()]);
    }

    public static function setLine(Player $player, int $line, string $customName) {

        $entry = new ScorePacketEntry();
        $entry->objectiveName = "Core_sidebar";
        $entry->type = $entry::TYPE_FAKE_PLAYER;
        $entry->customName = $customName;
        $entry->score = $line;
        $entry->scoreboardId = $line;
        $pk = new SetScorePacket();
        $pk->type = $pk::TYPE_CHANGE;
        $pk->entries[] = $entry;
        $player->sendDataPacket($pk);

    }

    public static function sendScoreboard(Player $player) : void {
        $user = UserManager::getUser($player->getName());
        $money = $user->getPlayerMoney();
        $quests = $user->getDoneQuestCount();

        self::addScoreboard($player, "§8§l» §l§9DarkMoonPE.PL§r §8§l«");
        self::setLine($player, 1, "§r§8----------------------");
        self::setLine($player, 2, " §7Nick: §9§l" . $player->getName() . " ");
        self::setLine($player, 3, " §7Online administracji: §9§l" . count(Main::$adminsOnline) . " ");
        self::setLine($player, 4, " §7Online graczy: §9§l" . count(self::getServer()->getOnlinePlayers()) . " ");
        self::setLine($player, 5, " §7Pieniadze: §9§l" . $money . "§r§7zl ");
        self::setLine($player, 6, " §7Wykonanych questow: §9§l" . $quests . " ");
        self::setLine($player, 7, "§8----------------------");
        self::setLine($player, 8, " §9§l/ustawienia ");
    }
}