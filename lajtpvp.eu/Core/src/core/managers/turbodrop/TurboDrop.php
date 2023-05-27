<?php

declare(strict_types=1);

namespace core\managers\turbodrop;

class TurboDrop {

    public function __construct(
        private int $id,
        private string $founder,
        private bool $server,
        private int $expire,
        private bool $isFromDataBase = false
    ) {}

    public function getFounder() : string {
        return $this->founder;
    }

    public function isServer() : bool {
        return $this->server;
    }

    public function getExpireTime() : int {
        return $this->expire;
    }

    public function getId() : int {
        return $this->id;
    }

    public function isFromDataBase() : bool {
        return $this->isFromDataBase;
    }
}