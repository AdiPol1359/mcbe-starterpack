<?php

declare(strict_types=1);

namespace core\webhooks;

use JsonSerializable;

abstract class Webhook implements JsonSerializable {

    protected array $data = [];

    public function jsonSerialize() : array {
        return $this->data;
    }
}
