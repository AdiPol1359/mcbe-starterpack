<?php

namespace core\form\forms\money;

use core\form\forms\Error;
use core\form\BaseForm;
use core\user\UserManager;
use core\util\utils\MessageUtil;
use pocketmine\Player;
use pocketmine\Server;

class OpenForm extends BaseForm {

    public function __construct(Player $player) {

        $money = UserManager::getUser($player->getName())->getPlayerMoney();

        $data = [
            "type" => "custom_form",
            "title" => "§8Stan konta: §l§9{$money}§8zl",
            "content" => [["type" => "input", "text" => "§7Podaj nick osoby ktorej chcesz zobaczyc stan konta", "placeholder" => "Steve", "default" => null]]
        ];

        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        if(empty($data[0]))
            return;

        if($data[0] == null)
            return;

        $p = Server::getInstance()->getPlayer($data[0]);
        $target = $p !== null ? $p->getName() : $data[0];

        if(!UserManager::userExists($target)) {
            $player->sendForm(new Error($player, "Ten gracz nie istnieje!", $this));
            return;
        }

        $money = UserManager::getUser($target)->getPlayerMoney();
        $player->sendMessage(MessageUtil::format("Stan konta gracza o nicku §l§9$target §r§7wynosi: §l§9{$money}§r§7zl"));
    }
}