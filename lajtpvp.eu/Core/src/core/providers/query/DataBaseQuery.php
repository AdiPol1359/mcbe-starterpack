<?php

declare(strict_types=1);

namespace core\providers\query;

use core\Main;

class DataBaseQuery {

    public function __construct(private Main $plugin) {}

    public function sendDefaultQueries() : void {
        $this->plugin->getProvider()->executeQueries(stream_get_contents($this->plugin->getResource("queries.sql")));
    }
}