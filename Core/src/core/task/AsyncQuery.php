<?php

namespace core\task;

use core\manager\managers\MySQLManager;
use mysqli;
use pocketmine\scheduler\AsyncTask;

abstract class AsyncQuery extends AsyncTask {

    protected function getMysqli(): mysqli {
        return new mysqli(MySQLManager::MYSQL_HOST, MySQLManager::MYSQL_USER, MySQLManager::MYSQL_PASSWORD, MySQLManager::MYSQL_DB);
    }

    protected function rebuildResult(array $result): array {
        $return = [];
        foreach ($result as $index => $array) {
            foreach ($array as $value) {
                $return[$index] = $value;
            }
        }
        return $return;
    }
}