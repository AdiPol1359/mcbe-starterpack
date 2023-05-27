<?php

declare(strict_types=1);

namespace core\utils;

use core\tasks\async\WebhookSendAsyncTask;
use core\webhooks\Webhook;
use pocketmine\Server;

final class WebhookUtil {

    public function __construct(){}

    public static function sendWebhook(Webhook $message, string $url) : void {
        Server::getInstance()->getAsyncPool()->submitTask(new WebhookSendAsyncTask($message, $url));
    }
}