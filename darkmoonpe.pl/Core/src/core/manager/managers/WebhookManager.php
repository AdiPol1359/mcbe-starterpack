<?php

namespace core\manager\managers;

use core\manager\BaseManager;
use core\webhook\Webhook;

class WebhookManager extends BaseManager {
    public static function sendWebhook(Webhook $message, string $url) : void {
        $wb = curl_init($url);
        curl_setopt($wb, CURLOPT_POSTFIELDS, json_encode($message));
        curl_setopt($wb, CURLOPT_POST, true);
        curl_setopt($wb, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($wb, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($wb, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($wb, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_exec($wb);
        curl_close($wb);
    }
}