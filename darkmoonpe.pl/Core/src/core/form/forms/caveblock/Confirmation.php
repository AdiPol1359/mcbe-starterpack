<?php

namespace core\form\forms\caveblock;

use core\caveblock\Cave;
use core\form\forms\Error;
use core\form\BaseForm;
use core\Main;
use core\util\utils\MessageUtil;
use pocketmine\Player;
use pocketmine\Server;

class Confirmation extends CaveForm {

    public const OWNER = 0;
    public const DELETE_ISLE = 1;
    public const LEAVE_ISLE = 2;
    private BaseForm $back;
    private int $type;

    public function __construct(int $type, Cave $cave, BaseForm $back) {

        parent::__construct($cave);

        $data = [
            "type" => "form",
            "title" => "§l§9POTWIERDZENIE",
            "content" => "",
            "buttons" => []
        ];

        if($type == self::DELETE_ISLE)
            $data["buttons"][] = ["text" => "§8§l» §9Usun §8§l«§r\n§8Kliknij aby usunac jaskinie na stale!"];

        if($type == self::OWNER)
            $data["buttons"][] = ["text" => "§8§l» §9Oddaj wlasciciela §8§l«§r\n§8Kliknij aby oddac!"];

        if($type == self::LEAVE_ISLE)
            $data["buttons"][] = ["text" => "§8§l» §9Opusc wyspe §8§l«§r\n§8Kliknij aby opuscic!"];

        $data["buttons"][] = ["text" => "§8§l» §9Anuluj §8§l«§r\n§8Kliknij aby Anulowac!"];

        $this->data = $data;
        $this->type = $type;
        $this->back = $back;

    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        parent::handleResponse($player, $data);

        switch($data) {

            case "0":

                if($this->type == self::OWNER) {
                    if(!$this->cave->getPlayerSetting($player->getName(), "z_perm")) {
                        $player->sendForm(new Error($player, "Nie masz uprawnien aby to zrobic", $this));
                        return;
                    }
                    $this->cave->switchOwner(Main::$selectedPlayer[$player->getName()]);
                    $player->sendMessage(MessageUtil::format("Poprawnie oddales wlasciciela jaskini"));
                    $this->cave->setSpawn($this->cave->getSpawn());
                }

                if($this->type == self::DELETE_ISLE) {
                    if(!$this->cave->getPlayerSetting($player->getName(), "z_perm")) {
                        $player->sendForm(new Error($player, "Nie masz uprawnien aby to zrobic", $this));
                        return;
                    }
                    $this->cave->remove();
                    $player->sendMessage(MessageUtil::format("Poprawnie usunales jaskienie"));
                }

                if($this->type == self::LEAVE_ISLE) {
                    $this->cave->kickPlayer($player->getName());
                    if($owner = Server::getInstance()->getPlayerExact($this->cave->getOwner()))
                        $owner->sendMessage(MessageUtil::format("Czlonek jaskini §l§9".$player->getName()."§r§7 opuscil twoja jaskienie o tagu §l§9".$this->cave->getName()));
                    $player->sendMessage(MessageUtil::format("Poprawnie opusciles jaskienie"));
                }

                break;
            case "1":
                $player->sendForm($this->back);
                break;
        }
    }
}