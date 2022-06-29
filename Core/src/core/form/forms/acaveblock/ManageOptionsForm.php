<?php

namespace core\form\forms\acaveblock;

use core\{
    caveblock\Cave,
    caveblock\CaveManager,
    form\forms\caveblock\CaveForm,
    form\forms\Error,
    manager\managers\ParticlesManager,
    util\utils\MessageUtil};

use pocketmine\Player;

class ManageOptionsForm extends CaveForm {

    public function __construct(Cave $cave) {

        parent::__construct($cave);

        $data = [
            "type" => "form",
            "title" => "§l§9ADMIN CAVEBLOCK",
            "content" => "",
            "buttons" => []
        ];

        $data["buttons"][] = ["text" => "§8§l» §9Ustaw spawna jaskini §8§l«§r\n§8Kliknij aby ustawic"];
        $data["buttons"][] = ["text" => "§8§l» §9Ustaw quest mastera §8§l«§r\n§8Kliknij aby ustawic"];
        $data["buttons"][] = ["text" => "§8§l» §9Zarzadzaj permisjami §8§l«§r\n§8Kliknij aby zarzadzac"];
        $data["buttons"][] = ["text" => "§8§l» §9Cofnij §8§l«§r\n§8Kliknij aby cofnac", "image" => ["type" => "path", "data" => "textures/blocks/barrier"]];
        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        parent::handleResponse($player, $data);

        $inCave = CaveManager::getCave($player);

        switch($data) {

            case "0":

                if(!CaveManager::isInCave($player)) {
                    $player->sendForm(new Error($player, "Musisz byc w jaskini aby moc ustawic spawna", $this));
                    return;
                }

                if($inCave->getName() !== $this->cave->getName()) {
                    $player->sendForm(new Error($player, "Nie znajdujesz w sie w poprawnej jaskini!", $this));
                    return;
                }

                $this->cave->setSpawn($player->asVector3());
                $player->sendMessage(MessageUtil::format("Poprawnie ustawiono spawna!"));
                ParticlesManager::spawnFirework($player, $player->getLevel(), [[ParticlesManager::TYPE_STAR, ParticlesManager::COLOR_YELLOW], [ParticlesManager::TYPE_STAR, ParticlesManager::COLOR_GOLD]]);
                break;
            case "1":

                if(!CaveManager::isInCave($player)) {
                    $player->sendForm(new Error($player, "Musisz byc w jaskini aby moc ustawic spawna", $this));
                    return;
                }

                if($inCave->getName() !== $this->cave->getName()) {
                    $player->sendForm(new Error($player, "Nie znajdujesz w sie w poprawnej jaskini!", $this));
                    return;
                }

                $this->cave->unsetVillager();
                $this->cave->setVillager($player->asVector3(), $player->getYaw());
                $player->sendMessage(MessageUtil::format("Poprawnie ustawiono villagera!"));
                ParticlesManager::spawnFirework($player, $player->getLevel(), [[ParticlesManager::TYPE_STAR, ParticlesManager::COLOR_WHITE], [ParticlesManager::TYPE_STAR, ParticlesManager::COLOR_BLACK]]);
                break;
            case "2":
                $player->sendForm(new ManageCavePermission($this->cave));
                break;
            case "3":
                $player->sendForm(new ManageCaveForm($this->cave));
                break;
        }
    }
}