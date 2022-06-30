<?php

namespace core\form\forms;

use core\command\CommandManager;
use core\form\BaseForm;
use core\Main;
use pocketmine\Player;

class HelpForm extends BaseForm {

    public function __construct(Player $player) {

        $data = [
            "type" => "form",
            "title" => "§9§lPOMOC",
            "content" => "",
            "buttons" => []
        ];

        foreach(CommandManager::$commands as $command) {
            if($command->getUsePermission() !== null) {
                if($player->hasPermission($command->getUsePermission()))
                    $data["buttons"][] = ["text" => "§8§l» §8/§9" . $command->getName() . " §8§l«§r\n§8Kliknij po wiecej informacji", "id" => count($data["buttons"]), "command" => $command];
                continue;
            } else
                $data["buttons"][] = ["text" => "§8§l» §8/§9" . $command->getName() . " §8§l«§r\n§8Kliknij po wiecej informacji", "id" => count($data["buttons"]), "command" => $command];
        }
        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        foreach($this->data["buttons"] as $index)
            if($index["id"] === $data)
                if(strpos($index["text"], $index["command"]->getName()))
                    $player->sendForm(new HelpInfoForm("§l§8/§9" . $index["command"]->getName(), $index["command"]->getHelpDescription()));
    }
}