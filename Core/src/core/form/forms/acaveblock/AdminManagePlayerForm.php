<?php

namespace core\form\forms\acaveblock;

use core\caveblock\Cave;
use core\caveblock\CaveManager;
use core\form\forms\caveblock\CaveForm;
use core\form\forms\caveblock\Confirmation;
use core\form\forms\Error;
use core\Main;
use core\util\utils\MessageUtil;
use pocketmine\Player;
use pocketmine\Server;

class AdminManagePlayerForm extends CaveForm {

    public function __construct(Player $player, Cave $cave) {

        parent::__construct($cave);

        $p = Main::$selectedPlayer[$player->getName()];

        $data = [
            "type" => "form",
            "title" => "§9§lADMIN CAVEBLOCK",
            "content" => "§7Wybrany gracz: §9§l$p",
            "buttons" => []
        ];

        $data["buttons"][] = ["text" => "§8§l» §9Zarzadzaj permisjami §8§l«§r\n§8Kliknij aby zarzadzac"];
        $data["buttons"][] = ["text" => "§8§l» §9Wyrzuc gracza §8§l«§r\n§8Kliknij aby wyrzucic"];
        $data["buttons"][] = ["text" => "§8§l» §9Oddaj wlasciciela §8§l«§r\n§8Kliknij aby oddac"];
        $data["buttons"][] = ["text" => "§8§l» §9Cofnij §8§l«§r\n§8Kliknij aby cofnac", "image" => ["type" => "path", "data" => "textures/blocks/barrier"]];

        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null) {
            $player->sendForm(new PlayerListForm($player, $this->cave));
            return;
        }

        parent::handleResponse($player, $data);

        switch($data) {

            case "0":
                $player->sendForm(new AdminManagePlayerPermissionForm($player, $this->cave));
                break;
            case "1":
                $player->sendMessage(MessageUtil::format("Poprawnie wyrzuciles gracza o nicku §l§9" . Main::$selectedPlayer[$player->getName()]));
                $this->cave->kickPlayer(Main::$selectedPlayer[$player->getName()]);
                break;
            case "2":
                isset(Main::$selectedPlayer[$player->getName()]) ? $p = Server::getInstance()->getPlayerExact(Main::$selectedPlayer[$player->getName()]) : $p = null;

                if($p === null) {
                    $player->sendForm(new Error($player, "Ten gracz musi byc online zeby moc oddac mu wlasciciela!", $this));
                    return;
                }

                if(CaveManager::getMaxPlayerCaves($p) <= CaveManager::caveCount($p)) {
                    $player->sendForm(new Error($player, "Ten gracz osiagnal limit jaskin!", $this));
                    return;
                }

                $player->sendForm(new Confirmation(Confirmation::OWNER, $this->cave, $this));
                break;
            case "3":

                $player->sendForm(new PlayerListForm($player, $this->cave));

                break;
        }
    }
}