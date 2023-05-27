<?php

declare(strict_types=1);

namespace core\webhooks\types;

use core\utils\Settings;
use core\webhooks\Webhook;

class Message extends Webhook {

    public function __construct(string $content, Embed $embed = null, string $url = "https://i.ibb.co/w7qxGDq/Untitled1881.png") {
        $this->data = [
            "content" => $content,
            "username" => Settings::$SERVER_NAME,
            "avatar_url" => $url,
        ];

        if(!empty($embed)) {
            $this->data["embeds"][] = $embed;
        }
    }
}