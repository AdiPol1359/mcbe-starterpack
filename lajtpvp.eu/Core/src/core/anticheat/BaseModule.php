<?php

declare(strict_types=1);

namespace core\anticheat;

use core\Main;
use core\utils\BroadcastUtil;
use core\utils\PermissionUtil;
use core\utils\Settings;
use core\utils\MessageUtil;
use core\utils\WebhookUtil;
use core\webhooks\types\Message;
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

    public function notifyAdmin(string $nick, string $info = "") : void {

        if(!($player = Server::getInstance()->getPlayerExact($nick)))
            return;

        WebhookUtil::sendWebhook(new Message("`".$nick . " (" . $player->getNetworkSession()->getPing() . "ms) " . $this->moduleName  . ($info !== "" ? " " . $info : "") . "" . "`"), Settings::$ANTICHEAT_WEBHOOK);

        BroadcastUtil::broadcastCallback(function($onlinePlayer) use ($player, $info, $nick) : void {
            if(PermissionUtil::has($onlinePlayer, Settings::$PERMISSION_TAG."antycheat")) {
                if(!($user = Main::getInstance()->getUserManager()->getUser($onlinePlayer->getName())))
                    return;

                $dataInfo = $this->str_split_unicode($info);

                foreach($dataInfo as $key => $str) {
                    if($str === "§") {
                        unset($dataInfo[$key], $dataInfo[$key + 1]);
                    }
                }

                if($user->hasAntiCheatAlerts())
                    $onlinePlayer->sendMessage(MessageUtil::anticheatFormat("§r§7" . $nick . " §8(§7" . $player->getNetworkSession()->getPing() . "ms§8) §c" . $this->moduleName . ($info !== "" ? " " . $info : "")));
            }
        });

    }

    function str_split_unicode($str, $l = 0) : array {
        if ($l > 0) {
            $ret = [];
            $len = mb_strlen($str, "UTF-8");
            for ($i = 0; $i < $len; $i += $l) {
                $ret[] = mb_substr($str, $i, $l, "UTF-8");
            }
            return $ret;
        }
        return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
    }
}