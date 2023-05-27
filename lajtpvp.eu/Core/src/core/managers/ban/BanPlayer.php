<?php

declare(strict_types=1);

namespace core\managers\ban;

use JetBrains\PhpStorm\Pure;

class BanPlayer {

    private string $nick;
    private ?string $ip;
    private ?string $deviceId;
    private string $admin;
    private string $reason;

    private int $start;
    private int $end;

    public function __construct(string $nick, ?string $ip, ?string $deviceId, string $admin, string $reason, int $start, int $end) {
        $this->nick = $nick;
        $this->ip = $ip;
        $this->deviceId = $deviceId;
        $this->admin = $admin;
        $this->reason = $reason;
        $this->start = $start;
        $this->end = $end;
    }

    public function getName() : string {
        return $this->nick;
    }

    public function getIp() : ?string {
        return $this->ip;
    }

    #[Pure] public function getDecodedIp() : string {
        return base64_decode($this->ip);
    }

    public function getDeviceId() : ?string {
        return $this->deviceId;
    }

    public function getAdmin() : string {
        return $this->admin;
    }

    public function getReason() : string {
        return $this->reason;
    }

    public function getStartBanTime() : int {
        return $this->start;
    }

    public function getEndBanTime() : int {
        return $this->end;
    }
}