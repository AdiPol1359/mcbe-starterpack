<?php

declare(strict_types=1);

namespace core\managers\mute;

class MutePlayer {

    private string $nick;
    private string $admin;
    private string $reason;

    private int $start;
    private int $end;

    public function __construct(string $nick, string $admin, string $reason, int $start, int $end) {
        $this->nick = $nick;
        $this->admin = $admin;
        $this->reason = $reason;
        $this->start = $start;
        $this->end = $end;
    }

    public function getName() : string {
        return $this->nick;
    }

    public function getAdmin() : string {
        return $this->admin;
    }

    public function getReason() : string {
        return $this->reason;
    }

    public function getStartMuteTime() : int {
        return $this->start;
    }

    public function getEndMuteTime() : int {
        return $this->end;
    }
}