<?php

namespace core\form\forms\services;

use core\form\BaseForm;
use core\manager\managers\service\ServicesManager;
use core\user\UserManager;
use pocketmine\Player;

class ServiceListForm extends BaseForm {
    public function __construct(Player $player) {

        $data = [
            "type" => "form",
            "title" => "§l§9TWOJE USLUGI",
            "content" => "",
            "buttons" => []
        ];

        $services = UserManager::getUser($player->getName())->getServices();

        foreach($services as $id => $serviceInfo)
            $data["buttons"][] = ["text" => "§8§l» §8§r#§l§9".$id." §8§l«§r\n§9".ServicesManager::getService($serviceInfo["service"])->getName(), "id" => count($data["buttons"]), "serviceId" => $id];

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        $user = UserManager::getUser($player->getName());

        $services = $user->getServices();

        foreach($services as $id => $serviceInfo) {
            foreach($this->data["buttons"] as $index) {
                if($index["id"] === $data && $id === $index["serviceId"]) {
                    $player->sendForm(new PlayerServiceInfo($id, $serviceInfo["service"], $serviceInfo["collected"], $serviceInfo["time"]));
                    return;
                }
            }
        }
    }
}