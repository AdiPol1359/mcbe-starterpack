<?php

namespace core\form\forms\services;

use core\form\BaseForm;
use core\form\forms\Error;
use core\manager\managers\ParticlesManager;
use core\manager\managers\ServerManager;
use core\manager\managers\service\ServicesManager;
use core\user\UserManager;
use core\util\utils\MessageUtil;
use pocketmine\Player;

class PlayerServiceInfo extends BaseForm {

    private int $id;
    private string $command;

    public function __construct(int $id, int $service, bool $collected, int $time) {

        $collected ? $status = "§l§aODEBRANO" : $status = "§l§cNIE ODEBRANO";
        $date = gmdate("d.m.Y H:i", $time);

        $services = ServicesManager::getService($service);

        $data = [
            "type" => "modal",
            "title" => "§9§lTWOJA USLUGA Z ID §r§8#§l§9".$id,
            "content" => "§7id:§l§9 {$id}\n§r§7status:§l§9 {$status}\n§r§7nazwa uslugi:§l§9 {$services->getName()}\n§r§7koszt uslugi:§l§9 {$services->getCost()}zl\n§r",
            "button1" => "§8§l» §9Odbierz §8§l«§r",
            "button2" => "§8§l» §9Cofnij §8§l«§r"
        ];

        if($time > 0)
            $data["content"] .= "§r§7data odebrania:§9§l {$date}";

        $this->command = $services->getCommand();
        $this->id = $id;
        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if(!$data) {
            $player->sendForm(new ServiceListForm($player));
            return;
        }

        if(!ServerManager::isSettingEnabled(ServerManager::ITEMSHOP)) {
            $player->sendForm(new Error($player, "Itemshop jest aktualnie wylaczony!", $this));
            return;
        }

        $user = UserManager::getUser($player->getName());

        if($user->isCollected($this->id)){
            $player->sendForm(new Error($player, "Juz odebrales ta usluge!", $this));
            return;
        }

        $player->sendMessage(MessageUtil::formatLines(["Wlasnie aktywowales swoja usluge!", "Dziekujemy za wspracie naszego serwera!"]));
        $user->claimReward($this->id, $this->command);
        ParticlesManager::spawnFirework($player, $player->getLevel(), [[ParticlesManager::TYPE_STAR, ParticlesManager::COLOR_YELLOW], [ParticlesManager::TYPE_STAR, ParticlesManager::COLOR_GOLD]]);
    }
}