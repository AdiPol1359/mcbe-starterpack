<?php

namespace core\form\forms\services;

use core\form\BaseForm;
use core\manager\managers\service\ServicesManager;
use pocketmine\Player;

class ServiceInfo extends BaseForm {

    public function __construct(int $id, string $nick, int $service, bool $collected, int $time) {

        $collected ? $status = "§l§aODEBRANO" : $status = "§l§cNIE ODEBRANO";
        $date = gmdate("d.m.Y H:i", $time);

        $service = ServicesManager::getService($service);

        $data = [
            "type" => "modal",
            "title" => "§9§lLog §r§8#§l§9".$id,
            "content" => "§7id:§l§9 {$id}\n§r§7nick:§l§9 {$nick}\n§r§7status:§l§9 {$status}\n§r§7nazwa uslugi:§l§9 {$service->getName()}\n§r§7koszt uslugi:§l§9 {$service->getCost()}zl\n",
            "button2" => "§8§l» §9Wyjscie §8§l«§r",
            "button1" => "§8§l» §9Cofnij §8§l«§r"
        ];

        if($time > 0)
            $data["content"] .= "§r§7data odebrania:§9§l {$date}";

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {
        if($data == 1)
            $player->sendForm(new ServiceLogForm());
    }
}