<?php

declare(strict_types=1);

namespace core\tasks\async;

use core\webhooks\Webhook;
use pocketmine\scheduler\AsyncTask;

class WebhookSendAsyncTask extends AsyncTask {

    private Webhook $webhook;
    private string $url;

    public function __construct(Webhook $webhook, string $url) {
        $this->webhook = $webhook;
        $this->url = $url;
    }

    public function onRun() : void {
        $wb = curl_init($this->url);
        curl_setopt($wb, CURLOPT_POSTFIELDS, json_encode($this->webhook));
        curl_setopt($wb, CURLOPT_POST, true);
        curl_setopt($wb, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($wb, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($wb, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($wb, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_exec($wb);
        curl_close($wb);
    }
}