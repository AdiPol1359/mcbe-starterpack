<?php

namespace core\form\forms\amoney;

use core\form\forms\Error;
use core\form\BaseForm;
use core\manager\managers\{
    LogManager,
    MoneyManager};
use core\user\UserManager;
use core\util\utils\MessageUtil;
use pocketmine\Player;
use pocketmine\Server;

class AddMoneyForm extends BaseForm {

    public function __construct() {

        $data = [
            "type" => "custom_form",
            "title" => "§9§lDodaj pieniadze",
            "content" => []
        ];

        $data["content"][] = ["type" => "input", "text" => "§7Wpisz nick gracza, ktoremu chcesz dodac pieniadze", "placeholder" => "Steve", "default" => null];
        $data["content"][] = ["type" => "input", "text" => "§7Podaj ilosc jaka chcesz dodac graczu", "placeholder" => "0.10", "default" => null];

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if(empty($data[0]) || empty($data[1]))
            return;

        $p = Server::getInstance()->getPlayerExact($data[0]);
        $target = $p !== null ? $p->getName() : $data[0];

        if(!MoneyManager::exists($target)) {
            $player->sendForm(new Error($player, "Ten gracz nie istnieje!", $this));
            return;
        }

        if(!is_numeric($data[1])) {
            $player->sendForm(new Error($player, "Ilosc musi byc zapisana numerycznie", $this));
            return;
        }

        $money = (float) number_format($data[1], 2, '.', '');

        if($money <= 0){
            $player->sendForm(new Error($player, "Ilosc musi byc wieksza jak §l§90§r§7!", $this));
            return;
        }

        UserManager::getUser($target)->addPlayerMoney($money);
        LogManager::sendLog($player, "AddMoney: ".$money."zl [".$target."]", LogManager::ADMIN_MONEY);

        $player->sendMessage(MessageUtil::format("Poprawnie dodales §l§9".$money."§r§7zl graczu o nicku §9§l{$target}§r§7!"));
    }
}