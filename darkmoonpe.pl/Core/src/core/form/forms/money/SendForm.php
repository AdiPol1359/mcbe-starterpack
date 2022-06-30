<?php

namespace core\form\forms\money;

use core\form\forms\Error;
use core\form\BaseForm;
use core\manager\managers\LogManager;
use core\user\UserManager;
use core\util\utils\MessageUtil;
use pocketmine\Player;
use pocketmine\Server;

class SendForm extends BaseForm {

    public function __construct(Player $player) {

        $money = UserManager::getUser($player->getName())->getPlayerMoney();

        $data = [
            "type" => "custom_form",
            "title" => "§8Stan konta: §l§9{$money}§8zl",
            "content" => [["type" => "input", "text" => "§7Wpisz nick gracza, do ktorego chcesz wyslac pieniadze", "placeholder" => "Steve", "default" => null], ["type" => "input", "text" => "§7Ilosc pieniedzy zabrana z twojego konta i wyslana do gracza z nickiem wpisanym wyzej", "placeholder" => "0.10", "default" => null]]
        ];

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if(empty($data[0]) || empty($data[1]))
            return;

        if($data[0] == null && $data[1] == null)
            return;

        $p = Server::getInstance()->getPlayer($data[0]);
        $target = $p !== null ? $p->getName() : $data[0];

        if(!UserManager::userExists($target)) {
            $player->sendForm(new Error($player, "Ten gracz nie istnieje!", $this));
            return;
        }

        if(!is_numeric($data[1])) {
            $player->sendForm(new Error($player, "Ilosc musi byc numeryczna!", $this));
            return;
        }

        $money = (float) number_format($data[1], 2, '.', '');

        if($money <= 0){
            $player->sendForm(new Error($player, "Ilosc musi byc wieksza jak §l§90§r§7!", $this));
            return;
        }

        if($target === $player->getName()) {
            $player->sendForm(new Error($player, "Nie mozesz wyslac pieniedzy do samego siebie!", $this));
            return;
        }

        if(UserManager::getUser($player->getName())->getPlayerMoney() < $data[1]) {
            $player->sendForm(new Error($player, "Nie masz wystarczajaco duzo pieniedzy! Brakuje ci §l§9" . abs(UserManager::getUser($player->getName())->getPlayerMoney() - $data[1]) . "§r§7zl Aby wyslac §l§9" . $data[1] . "§r§7zl", $this));
            return;
        }

        UserManager::getUser($target)->addPlayerMoney($money);
        UserManager::getUser($player->getName())->reducePlayerMoney($money);
        LogManager::sendLog($player, "SendMoney: ".$money."zl [".$target."]", LogManager::MONEY);

        $player->sendMessage(MessageUtil::format("Poprawnie wyslales §l§9".$money."§r§7zl do gracza o nicku §l§9{$target}§r§7!"));

        $selectedPlayer = Server::getInstance()->getPlayerExact($target);

        if(!$selectedPlayer)
            return;

        $selectedPlayer->sendMessage(MessageUtil::format("Gracz o nicku §l§9".$player->getName()."§r§7 wyslal do ciebie §l§9".$money."§r§7zl"));
    }
}