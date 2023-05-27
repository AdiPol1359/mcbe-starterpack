<?php

declare(strict_types=1);

namespace core\webhooks\types;

use core\webhooks\Webhook;

class Embed extends Webhook {

    public function __construct(string $title = "null", string $description = "null", array $fields = null, bool $footer = false, string $thumbnail = null, int $color = 0xf59e42) {
        $this->data = [
            "title" => $title,
            "description" => $description,
            "color" => $color,
            "fields" => []
        ];

        if($fields != null) {
            $this->data["fields"] = $fields;
        }

        if($thumbnail != null) {
            $this->data["thumbnail"]["url"] = $thumbnail;
        }

        if($footer == true) {
            $this->data["footer"]["text"] = date("d.m.Y H:i", time());
        }
    }
}