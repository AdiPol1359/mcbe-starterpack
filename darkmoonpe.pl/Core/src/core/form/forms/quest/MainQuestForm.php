<?php

namespace core\form\forms\quest;

use core\form\BaseForm;
use core\form\forms\Confirmation;
use core\form\forms\skills\SkillMainForm;
use core\manager\managers\ParticlesManager;
use core\manager\managers\quest\QuestManager;
use core\manager\managers\SoundManager;
use core\user\UserManager;
use core\util\utils\MessageUtil;
use core\util\utils\TimeUtil;
use pocketmine\Player;

class MainQuestForm extends BaseForm {

    public function __construct(Player $player) {

        $userManager = UserManager::getUser($player->getName());

        $data = [
            "type" => "form",
            "title" => "§l§9QUEST MASTER",
            "content" => "",
            "buttons" => []
        ];

        $timestamp = $userManager->getTimestamp();

        $quest = $userManager->getSelectedQuest();

        if($quest) {
            $questName = $quest->getCleanName();
            $rewardName = $quest->getCleanRewardName();
        }

        $data["buttons"][] = ["text" => "§8§l» §9Umiejetnosci §8§l«§r\n§8Kliknij aby zobaczyc"];

        $data["buttons"][] = ["text" => "§8§l» §9Wybierz questa §8§l«§r\n§8Kliknij aby wybrac"];

        if($quest) {
            if($userManager->isDoneQuest())
                $data["buttons"][] = ["text" => "§8§l» §rKliknij aby odebrac nagrode §8§l«§r\n§9" . $rewardName];

            else {
                $data["buttons"][] = ["text" => "§8§l» §9Anuluj questa §8§l«§r\n§8Kliknij aby anulowac"];

                $status = $userManager->getQuestStatus();
                $max_status = $quest->getMaxTimes();

                $data["content"] = "§r§7Wybrany quest: §9{$questName}\n§r§7Stan questa: §8(§9{$status}§7/§9{$max_status}§8)\n§r§7Wykonanych questow: §9" . $userManager->getDoneQuestCount() . "\n§r§7Nagroda: §9" . $rewardName;
            }
        }

        $data["content"] .= ($data["content"] !== "" ? "\n\n" : "")."§r§7Nastepne questy za: \n§8(§9".TimeUtil::convertIntToStringTime(($timestamp - time()), "§9")."§8)\n";

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        $userManager = UserManager::getUser($player->getName());

        switch($data) {
            case "0":
                $player->sendForm(new SkillMainForm($player));
                break;

            case "1":
                $player->sendForm(new ChooseQuestForm($player));
                break;

            case "2":

                if(!$userManager->isSelectedQuest()) {
                    $player->sendForm(new ChooseQuestForm($player));
                } else if($userManager->isDoneQuest()) {
                    $userManager->nextQuest();
                    QuestManager::update($player);
                    $player->sendMessage(MessageUtil::format("Odebrales nagrode za questa!"));
                    ParticlesManager::spawnFirework($player, $player->getLevel(), [[ParticlesManager::TYPE_SMALL_SPHERE, ParticlesManager::COLOR_DARK_PURPLE], [ParticlesManager::TYPE_SMALL_SPHERE, ParticlesManager::COLOR_BLUE]]);
                } else {

                    $player->sendForm(new Confirmation("§l§9ANULOWANIE QUESTA", "§7Czy na pewno chcesz anulowac questa?\nWszystko co dotychczasz zrobiles w tym quescie zostanie zresetowane!", "§l§9RESETUJ QUESTA", "§8Anuluj",
                        function() use ($player, $userManager) : void {
                            $userManager->resetQuest();
                            $player->sendMessage(MessageUtil::format("Anulowales questa!"));
                            SoundManager::addSound($player, $player->asPosition(), "mob.cat.meow");
                            QuestManager::update($player);
                        }, function() use ($player) : void { $player->sendForm(new $this($player)); }));
                }
                break;
        }
    }
}