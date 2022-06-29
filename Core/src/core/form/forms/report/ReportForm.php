<?php

namespace core\form\forms\report;

use core\form\BaseForm;
use core\form\forms\Error;
use core\user\UserManager;
use core\util\utils\ConfigUtil;
use pocketmine\Player;

class ReportForm extends BaseForm {

    public function __construct() {

        $data = [
            "type" => "custom_form",
            "title" => "§l§9ZGLOSZENIE",
            "content" => []
        ];

        $options = ["Scam", "Cheater", "Znalazlem buga", "Inne"];

        $data["content"][] = ["type" => "dropdown", "text" => "\n§l§8§k1§r §l§9TYP ZGLOSZENIA§8 §k1", "options" => $options, "default" => null];
        $data["content"][] = ["type" => "input", "text" => "§7Wprowadz tresc zgloszenia", "placeholder" => "", "default" => null];

        $dataContent = array_values($options);

        $data["options"] = $dataContent;

        $this->data = $data;
    }

    public function handleResponse(Player $player, $data) : void {

        if($data === null)
            return;

        $user = UserManager::getUser($player->getName());

        if(!$user)
            return;

        $option = "";

        foreach($this->data["options"] as $id => $name) {
            if($id === $data[0])
                $option = $name;
        }

        if($user->getLastReport() > time()){
            $player->sendForm(new Error($player, "Za szybko wysylasz zgloszenia, odczekaj §l§9".gmdate("i:s", $user->getLastReport() - time())."§r§7 zanim znow wyslesz zgloszenie!", $this));
            return;
        }

        if($data[0] === null || $data[1] === null || $option === ""){
            $player->sendForm(new Error($player, "Nie wprowadzono wymaganych informacji!", $this));
            return;
        }

        $message = "[";
        $administrators = [];
        $important = "";

        switch($option) {

            case "Cheater":

                $administrators[] = ConfigUtil::DISCORD_ADMINISTRATOR_ROLES["Admin"];
                $administrators[] = ConfigUtil::DISCORD_ADMINISTRATOR_ROLES["ROOT"];

                $important = "Podczas zglaszania cheatera warto wlaczyc nagrywanie aby miec dowod w celu latwiejszego udowodnienia cheatow!";
                break;
            case "Scam":

                $administrators[] = ConfigUtil::DISCORD_ADMINISTRATOR_ROLES["Admin"];
                $administrators[] = ConfigUtil::DISCORD_ADMINISTRATOR_ROLES["ROOT"];

                $important = "Podczas zglaszania scamu warto zrobic screenshoty wiadomosci z oszustem aby miec dowod!";
                break;

            case "Inne":

                $administrators[] = ConfigUtil::DISCORD_ADMINISTRATOR_ROLES["Admin"];
                $administrators[] = ConfigUtil::DISCORD_ADMINISTRATOR_ROLES["ROOT"];
                $administrators[] = ConfigUtil::DISCORD_ADMINISTRATOR_ROLES["Support"];

                $important = "Oczekuj na administratora, wkrotce dolaczy na serwer i pomoze rozwiazac ci twoj problem!";

                break;
            case "Znalazlem buga":

                $administrators[] = ConfigUtil::DISCORD_ADMINISTRATOR_ROLES["Admin"];
                $administrators[] = ConfigUtil::DISCORD_ADMINISTRATOR_ROLES["ROOT"];
                $administrators[] = ConfigUtil::DISCORD_ADMINISTRATOR_ROLES["Support"];

                $important = "W celu latwiejszego naprawienia bledu sprobuj nagrac co go powoduje i pokaz administratorowi!";
                break;
        }

        foreach($administrators as $key => $administrator){

            $message .= $administrator;

            if(array_key_last($administrators) !== $key)
                $message .= " / ";
        }

        $message .= "]";

        $player->sendForm(new ConfirmReportForm($data[1], $option, $message, $important));
    }
}