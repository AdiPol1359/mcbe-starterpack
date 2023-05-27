<?php

declare(strict_types=1);

namespace core\managers\admin;

class Admin {
    public function __construct(
        private string $nick,
        private int $spendTime,
        private int $messages,
        private int $bans,
        private int $mutes
    ) {}

    public function getName() : string {
        return $this->nick;
    }

    public function getSpendTime() : int {
        return $this->spendTime;
    }

    public function addTime(int $value) : void {
        $this->spendTime += $value;
    }

    public function setTime(int $value) : void {
        $this->spendTime = $value;
    }

    public function getMessages() : int {
        return $this->messages;
    }

    public function addMessage(int $value) : void {
        $this->messages += $value;
    }

    public function setMessages(int $value) : void {
        $this->messages = $value;
    }

    public function getBans() : int {
        return $this->bans;
    }

    public function addBan(int $value) : void {
        $this->bans += $value;
    }

    public function setBans(int $value) : void {
        $this->bans = $value;
    }

    public function getMutes() : int {
        return $this->mutes;
    }

    public function addMute(int $value) : void {
        $this->mutes += $value;
    }

    public function setMutes(int $value) : void {
        $this->mutes = $value;
    }
}