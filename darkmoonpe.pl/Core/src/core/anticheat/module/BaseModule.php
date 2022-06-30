<?php

namespace core\anticheat\module;

use core\util\utils\ConfigUtil;
use core\util\utils\MessageUtil;
use pocketmine\event\Listener;
use pocketmine\Server;

abstract class BaseModule implements Listener {

    private string $moduleName;
    protected array $data;
    protected bool $enabled;

    public function __construct(string $moduleName) {
        $this->moduleName = $moduleName;
        $this->data = [];
        $this->enabled = true;
    }

    public function getModuleName() : string {
        return $this->moduleName;
    }

    public function isModuleEnabled() : bool {
        return $this->enabled;
    }

    public function setModule(bool $status) : void {
        $this->enabled = $status;
    }

    public function getData() : array {
        return $this->data;
    }

    public function notifyAdmin(string $nick) : void {
        foreach(Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
            if($onlinePlayer->hasPermission(ConfigUtil::PERMISSION_TAG."antycheat"))
                $onlinePlayer->sendMessage(MessageUtil::anticheatFormat("§r§8(§7".$nick."§8) §7 Prawdopodobnie korzysta z §c".$this->moduleName."§r§7!"));
        }
    }
}