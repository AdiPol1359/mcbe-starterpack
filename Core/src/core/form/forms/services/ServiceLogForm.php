<?php

namespace core\form\forms\services;

use core\form\BaseForm;
use core\user\UserManager;
use pocketmine\Player;

class ServiceLogForm extends BaseForm {
    public function __construct() {

        $data = [
            "type" => "form",
            "title" => "§l§9LOGI USLUG",
            "content" => "",
            "buttons" => []
        ];

        foreach(UserManager::getUsers() as $user) {
            $services = $user->getServices();
            foreach($services as $id => $serviceInfo)
                $data["buttons"][] = ["text" => "§8§l» §8§r#§l§9" . $id . " §8§l«§r\n§8Klient §l§9" . $serviceInfo["nick"], "id" => $id, "buttonId" => count($data["buttons"])];
        }

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        foreach($this->data["buttons"] as $index) {

            if(!isset($index["buttonId"]))
                continue;

            if($index["buttonId"] !== $data)
                continue;

            foreach(UserManager::getUsers() as $user) {
                $services = $user->getServices();

                if($services === null)
                    continue;

                foreach($services as $id => $serviceInfo) {
                    if($index["id"] === $id) {
                        $player->sendForm(new ServiceInfo($id, $serviceInfo["nick"], $serviceInfo["service"], $serviceInfo["collected"], $serviceInfo["time"]));
                        return;
                    }
                }
            }
        }
    }
}