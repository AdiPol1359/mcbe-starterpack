<?php

namespace core\webhook\types;

use core\webhook\Webhook;

class Message extends Webhook {

    public function __construct(string $content, Embed $embed = null, string $url = "https://i.ibb.co/9vyMQ0C/logoDM2.png") {
        $this->data = [
            "content" => $content,
            "username" => "DarkMoonPE",
            "avatar_url" => $url,
        ];

        if(!empty($embed))
            $this->data["embeds"][] = $embed;
    }
}