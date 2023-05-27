<?php

declare(strict_types=1);

namespace core\users\data;

use core\Main;
use core\users\User;
use core\utils\Settings;

class UserIncognito {

    private array $data = [];

    public function __construct(private User $user) {
        $this->load();
    }

    public function load() : void {
        $provider = Main::getInstance()->getProvider();

        if(empty($provider->getQueryResult("SELECT nick FROM incognito WHERE nick = '".$this->user->getName()."'", true))) {
            $provider->executeQuery("INSERT INTO incognito (nick, name, skin, tag) VALUES ('" . $this->user->getName() . "', 0, 0, 0)");
        }

        $incognitoData = [];
        foreach($provider->getQueryResult("SELECT * FROM incognito WHERE nick = '".$this->user->getName()."'", true) as $key => $row) {
            foreach($row as $keyVal => $val) {
                if($keyVal === "nick") {
                    continue;
                }

                $incognitoData[$keyVal] = $val;
            }
        }

        $incognitoData[Settings::$DATA_INCOGNITO_NAME] = "";

        $this->data = $incognitoData;
    }

    public function save() : void {
        foreach($this->data as $index => $status) {
            if($index === Settings::$DATA_INCOGNITO_NAME) {
                continue;
            }

            Main::getInstance()->getProvider()->executeQuery("UPDATE incognito SET '{$index}' = '" . ($status ? 1 : 0) . "' WHERE nick = '{$this->user->getName()}'");
        }
    }

    public function getIncognitoData(string $data) : string|bool|int {
        return $this->data[$data];
    }

    public function setIncognitoData(string $data, $value) : void {
        $this->data[$data] = $value;
    }
}