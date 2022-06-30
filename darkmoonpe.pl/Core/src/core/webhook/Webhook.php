<?php

namespace core\webhook;

use JsonSerializable;

abstract class Webhook implements JsonSerializable {

    protected array $data = [];

    public function jsonSerialize() : array {
        return $this->data;
    }
}
