<?php

namespace core\form\forms\acaveblock;

use core\caveblock\Cave;
use core\form\forms\caveblock\CaveForm;
use pocketmine\Player;
use pocketmine\Server;

class ManageCavePermission extends CaveForm {

    public function __construct(Cave $cave) {

        parent::__construct($cave);

        $data = [
            "type" => "custom_form",
            "title" => "§l§9ADMIN CAVEBLOCK",
            "content" => []
        ];

        $data["content"][] = ["type" => "label", "text" => "§l§8§k1§r §l§9Blokady ustawienia ogolne §8§k1"];

        $data["content"][] = ["type" => "toggle", "text" => "§7Ogien bratobojczy", "default" => (bool) $this->cave->getCaveSetting("f_fire")];
        $data["content"][] = ["type" => "toggle", "text" => "§7Blokada odwiedzania", "default" => (bool) $this->cave->getCaveSetting("locked")];
        $data["content"][] = ["type" => "toggle", "text" => "§7Blokada niszczenia dla wszystkich procz wlasciciela", "default" => (bool) $this->cave->getCaveSetting("b_b_pl")];
        $data["content"][] = ["type" => "toggle", "text" => "§7Blokada budowania dla wszystkich procz wlasciciela", "default" => (bool) $this->cave->getCaveSetting("b_n_pl")];

        $data["content"][] = ["type" => "label", "text" => "§l§8§k1§r §l§9Blokady gdy wlasciciela nie ma na serwerze§8§k1"];

        $data["content"][] = ["type" => "toggle", "text" => "§7Blokada niszczenia gdy wlasciciela jaskini nie ma na serwerze dla czlonkow jaskini", "default" => (bool) $this->cave->getCaveSetting("b_b_off")];
        $data["content"][] = ["type" => "toggle", "text" => "§7Blokada budowania gdy wlasciciela jaskini nie ma na serwerze dla czlonkow jaskini", "default" => (bool) $this->cave->getCaveSetting("b_n_off")];

        $data["content"][] = ["type" => "label", "text" => "§l§8§k1§r §l§9Blokady od danych godzin §8§k1"];

        $data["content"][] = ["type" => "toggle", "text" => "§7Blokada niszczenia od danych godzin", "default" => (bool) $this->cave->getCaveSetting("b_b_time")];
        $data["content"][] = ["type" => "toggle", "text" => "§7Blokada budowania od danych godzin", "default" => (bool) $this->cave->getCaveSetting("b_n_time")];
        $data["content"][] = ["type" => "toggle", "text" => "§7Blokada interackji od danych godzin", "default" => (bool) $this->cave->getCaveSetting("i_b_time")];

        $data["content"][] = ["type" => "slider", "text" => "§7Blokada od godziny§9 ", "min" => 0, "max" => 24, "default" => $this->cave->getTimeSetting("f_time")];
        $data["content"][] = ["type" => "slider", "text" => "§7Blokada do godziny§9 ", "min" => 0, "max" => 24, "default" => $this->cave->getTimeSetting("t_time")];

        $this->data = $data;

    }

    public function handleResponse(Player $player, $data) : void {

        parent::handleResponse($player, $data);

        if(isset($data[2])) {
            if($data[2]) {
                $level = Server::getInstance()->getLevelByName($this->cave->getLevel());

                if(!$this->cave || !$level)
                    return;

                foreach($level->getPlayers() as $levelPlayer) {
                    if(!$this->cave->isMember($levelPlayer->getName()))
                        $levelPlayer->teleport(Server::getInstance()->getDefaultLevel()->getSafeSpawn());
                }
            }
        }

        if(isset($data[1]))
            $this->cave->switchSetting("f_fire", $data[1]);

        if(isset($data[2]))
            $this->cave->switchSetting("locked", $data[2]);

        if(isset($data[3]))
            $this->cave->switchSetting("b_b_pl", $data[3]);

        if(isset($data[4]))
            $this->cave->switchSetting("b_n_pl", $data[4]);

        if(isset($data[6]))
            $this->cave->switchSetting("b_b_off", $data[6]);

        if(isset($data[7]))
            $this->cave->switchSetting("b_n_off", $data[7]);

        if(isset($data[9]))
            $this->cave->switchSetting("b_b_time", $data[9]);

        if(isset($data[10]))
            $this->cave->switchSetting("b_n_time", $data[10]);

        if(isset($data[11]))
            $this->cave->switchSetting("i_b_time", $data[11]);

        if(isset($data[12]))
            $this->cave->switchTimeSetting("f_time", $data[12]);

        if(isset($data[13]))
            $this->cave->switchTimeSetting("t_time", $data[13]);

        $player->sendForm(new ManageOptionsForm($this->cave));
    }
}