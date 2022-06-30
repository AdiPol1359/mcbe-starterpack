<?php

namespace core\form\forms\skills;

use core\form\BaseForm;
use core\form\forms\Error;
use core\manager\managers\skill\SkillManager;
use core\user\UserManager;
use core\util\utils\MessageUtil;
use pocketmine\Player;

class SkillInfo extends BaseForm {

    private int $id;

    public function __construct(Player $player, int $id) {

        $skill = SkillManager::getSkill($id);
        $userManager = UserManager::getUser($player->getName());
        $userManager->hasSkill($id) ? $status = "KUPIONY" : $status = "NIE KUPIONY";

        $data = [
            "type" => "form",
            "title" => "§l§9" . $skill->getName(),
            "content" => "§9§lOpis umiejetnosci:\n§r§7" . $skill->getDescription() . "\n\n§r§7Stan: §l§9" . $status,
            "buttons" => []
        ];

        if(!$userManager->hasSkill($id))
            $data["buttons"][] = ["text" => "§8§l» §9KUP §8§l«§r\n§8Koszt: §9§l{$skill->getCost()}§8zl"];

        $data["buttons"][] = ["text" => "§8§l» §9Cofnij §8§l«§r\n§8Kliknij aby cofnac", "image" => ["type" => "path", "data" => "textures/blocks/barrier"]];

        $this->data = $data;
        $this->id = $id;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        $userManager = UserManager::getUser($player->getName());
        $skill = SkillManager::getSkill($this->id);

        switch($data) {
            case "0":
                if(!$userManager->hasSkill($this->id)) {
                    if($userManager->getPlayerMoney() >= $skill->getCost()) {
                        $userManager->reducePlayerMoney($skill->getCost());
                        $userManager->addSkill($skill->getId());
                        $player->sendMessage(MessageUtil::format("Poprawnie zakupiles umiejetnosc!"));
                        return;
                    } else
                        $player->sendForm(new Error($player, "Nie masz wystarczajaco duzo pieniedzy aby kupic ta umiejetnosc! Brakuje ci §l§9" . abs($skill->getCost() - $userManager->getPlayerMoney()) . "§7zl", $this));
                } else
                    $player->sendForm(new SkillShop());
                break;
            case "1":
                $player->sendForm(new SkillShop());
                break;
        }
    }

    // MOZESZ NIE ZNISZCZYC PRZEDMIOTU LEPSZE PRZEDMIOTY!
}