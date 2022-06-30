<?php

namespace core\manager\managers\quest;

use core\Main;
use core\manager\BaseManager;
use core\manager\managers\bossbar\Bossbar;
use core\manager\managers\bossbar\BossbarManager;
use core\manager\managers\bossbar\bossbars\AreaBoss;
use core\manager\managers\ParticlesManager;
use core\manager\managers\SettingsManager;
use core\manager\managers\SoundManager;
use core\user\UserManager;
use pocketmine\Player;

class QuestManager extends BaseManager {

    private static array $quests = [];
    private static array $message = [];

    public static function init() : void {
        Main::getDb()->exec("CREATE TABLE IF NOT EXISTS quest (nick TEXT, madeQuests INT, selected INT, status FLOAT, quests TEXT, questTime INT)");
    }

    public static function getQuest(int $id) : ?Quest {
        return self::$quests[$id] ?? null;
    }

    public static function loadQuests() : void {

        $db = Main::$quest;

        $quests = [];

        foreach($db as $row => $value)
            $quests[$row] = new Quest($row, $value["name"], $value["type"], $value["item_id"], $value["item_damage"], $value["max_count"], $value["reward"]["name"], $value["reward"]["type"], $value["reward"]["id"], $value["reward"]["damage"], $value["reward"]["count"]);

        self::$quests = $quests;
    }

    public static function registerPlayer(string $nick) : void {

        $randomQuests = [];

        for($i = 1; $i <= 5; $i++) {
            $quest = self::getRandomQuest();

            if(!$quest) {
                $i++;
                continue;
            }

            $randomQuests[$quest->getId()] = false;
        }

        if(empty(Main::getDb()->query("SELECT * FROM 'quest' WHERE nick = '$nick'")->fetchArray()))
            Main::getDb()->query("INSERT INTO quest (nick, madeQuests, selected, status, quests, questTime) VALUES ('$nick', 0, 0, 0, '".json_encode($randomQuests)."', '".(time() + 86400)."')");
    }

    public static function getMadeCount(int $id) : int {

        $index = 0;

        $users = UserManager::getUsers();

        foreach($users as $user) {
            if($user->getSelectedQuest()->getId() > $id)
                $index++;
        }

        return $index;
    }

    public static function getRandomQuest() : ?Quest {

        if(!self::$quests)
            return null;

        return self::getQuest(mt_rand(1, count(self::$quests))) ?? null;
    }

    public static function send(Player $player) : void {

        $bossbar = new Bossbar("");
        $userManager = UserManager::getUser($player->getName());

        if(!$userManager->isSettingEnabled(SettingsManager::QUEST_BOSSBAR)) {
            $bossbar->hideFrom($player);
            return;
        }
        if(BossbarManager::getBossbar($player) === null)
            $bossbar->showTo($player);

        if($userManager->hasMadeAllQuests()) {
            $bossbar->setSubTitle("§r"."§l§8» §r§7Wykonales wszystkie questy! §l§8«");
            $bossbar->setHealthPercent(1);
            return;
        }

        if(!$userManager->isSelectedQuest()) {
            $bossbar->setSubTitle("§r"."§l§8» §r§7Nie wybrano questa! §l§8«");
            $bossbar->setHealthPercent(0);
            return;
        }

        $quest = $userManager->getSelectedQuest()->getCleanName();
        $status = $userManager->getQuestStatus();
        $max_status = $userManager->getSelectedQuest()->getMaxTimes();
        $rewardName = $userManager->getSelectedQuest()->getRewardName();

        if($max_status != -1) {
            if($status < $max_status) {
                if(isset(self::$message[$player->getName()]))
                    unset(self::$message[$player->getName()]);

                $bossbar->setSubTitle("§r"."§7Quest §9".$quest."\n§r"."§7Status §8[§9".$status."§8/§9".$max_status."§8]"."§r\n"."§7Nagroda §9".$rewardName);
            } else {
                $bossbar->setSubTitle("§r"."§l§8» §r§7Masz wykonanego questa! §l§8«");

                if(isset(self::$message[$player->getName()])) {
                    self::$message[$player->getName()] = true;
                    $player->addTitle("§7Masz ukonczonego questa!");
                    ParticlesManager::sendTotem($player);
                    SoundManager::addSound($player, $player->asVector3(), "random.explode", 1);
                }
            }
        }

        $percentage = ($status / $max_status) * 100;
        $bossbar->setHealthPercent($percentage / 100);
    }

    public static function update(Player $player) : void {
        $bossbar = BossbarManager::getBossbar($player);
        $userManager = UserManager::getUser($player->getName());

        if(!$userManager->isSettingEnabled(SettingsManager::QUEST_BOSSBAR)) {
            $bossbar->hideFrom($player);
            return;
        }

        if(BossbarManager::getBossbar($player) == null)
            $bossbar->showTo($player);

        if($bossbar instanceof AreaBoss)
            return;

        if($userManager->hasMadeAllQuests()) {
            $bossbar->setSubTitle("§r"."§l§8» §r§7Wykonales wszystkie questy! §l§8«");
            $bossbar->setHealthPercent(1);
            return;
        }

        if(!$userManager->isSelectedQuest()) {
            $bossbar->setSubTitle("§r"."§l§8» §r§7Nie wybrano questa! §l§8«");
            $bossbar->setHealthPercent(0);
            return;
        }

        $quest = $userManager->getSelectedQuest()->getCleanName();
        $status = $userManager->getQuestStatus();
        $max_status = $userManager->getSelectedQuest()->getMaxTimes();
        $rewardName = $userManager->getSelectedQuest()->getRewardName();

        if($max_status != -1) {
            if($status < $max_status) {
                if(isset(self::$message[$player->getName()]))
                    unset(self::$message[$player->getName()]);

                $bossbar->setSubTitle("§r"."§7Quest §9".$quest."\n§r"."§7Status §8[§9".$status."§8/§9".$max_status."§8]"."§r\n"."§7Nagroda §9".$rewardName);
            } else {
                $bossbar->setSubTitle("§r"."§l§8» §r§7Masz wykonanego questa! §l§8«");

                if(!isset(self::$message[$player->getName()])) {
                    self::$message[$player->getName()] = true;
                    $player->addTitle("§7Ukonczyles questa!");
                    ParticlesManager::sendTotem($player);
                    SoundManager::addSound($player, $player->asVector3(), "random.explode", 1);
                }
            }
        }

        $percentage = ($status / $max_status) * 100;
        $bossbar->setHealthPercent($percentage / 100);
    }

    public static function getQuestArray() : array {
        return self::$quests;
    }
}