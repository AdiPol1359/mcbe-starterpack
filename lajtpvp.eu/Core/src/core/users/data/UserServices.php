<?php

declare(strict_types=1);

namespace core\users\data;

use core\Main;
use core\users\User;
use core\utils\Settings;
use core\utils\WebhookUtil;
use core\webhooks\types\Embed;
use core\webhooks\types\Message;
use JetBrains\PhpStorm\Pure;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\Server;

class UserServices {

    private array $data = [];

    public function __construct(private User $user) {
        $this->load();
    }

    public function load() : void {
        $services = Main::getInstance()->getServicesManager()->getServices($this->user->getName());

        foreach($services as $key => $serviceInfo)
            $this->data[] = $serviceInfo;
    }

    public function save() : void {
        $provider = Main::getInstance()->getProvider();
        foreach($this->data as $key => $value) {
            $collected = $value['collected'] ? 1 : 0;

            if(empty($provider->getQueryResult("SELECT * FROM service WHERE id = '".$value['id']."' AND nick = '{$this->user->getName()}'", true))) {
                $provider->executeQuery("INSERT INTO service (id, nick, service, collected, time) VALUES ('" . $value["id"] . "', '{$this->user->getName()}', '{$value['service']}', '$collected', '{$value['time']}')");
            } else {
                $provider->executeQuery("UPDATE service SET collected = '$collected', time = '{$value['time']}' WHERE id = '" . $value["id"] . "'");
            }
        }
    }

    public function addService(int $index) : void {
        $id = mt_rand(0, 10000);

        while(Main::getInstance()->getServicesManager()->existsService($id))
            $id = mt_rand(0, 10000);

        $service = Main::getInstance()->getServicesManager()->getService($index);

        if(!$service)
            return;

        $this->data[] = ["id" => $id, "nick" => $this->user->getName(), "service" => $index, "collected" => false, "time" => 0];
        WebhookUtil::sendWebhook(new Message("", new Embed("ZAKUP USLUGI", "\nNick: **" . $this->user->getName() . "**\n"."Nazwa uslugi: **" . $service->getName(). "**\n", null, true)), Settings::$ITEM_SHOP_WEBHOOK);
    }

    public function isCollected(int $id) : bool {
        foreach($this->data as $key => $service) {
            if($service["id"] == $id)
                return $this->data[$key]["collected"];
        }

        return true;
    }

    public function claimReward(int $id, string $command) : void {
        foreach($this->data as $key => $service) {
            if($service["id"] == $id) {
                $this->data[$key]["collected"] = true;
                $this->data[$key]["time"] = time();

                $cmd = str_replace("{nick}", '"'.$this->user->getName().'"', $command);

                $server = Server::getInstance();
                $server->dispatchCommand(new ConsoleCommandSender($server, $server->getLanguage()), $cmd);
            }
        }
    }

    #[Pure] public function hasService() : bool {
        return count($this->data) > 0;
    }

    public function hasServiceToCollect() : bool {
        foreach($this->data as $key => $service) {
            if(isset($this->data[$key])) {
                if(!$this->data[$key]["collected"])
                    return true;
            }
        }

        return false;
    }

    public function getServicesToCollect() : array {

        $services = [];

        foreach($this->data as $key => $service) {
            if(!$this->data[$key]["collected"])
                $services[] = $service;
        }

        return $services;
    }

    public function getServices() : array {
        return $this->data;
    }
}