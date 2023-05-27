<?php

declare(strict_types=1);

namespace core\managers;

use core\Main;

class WhitelistManager {

    public function __construct(private Main $plugin) {}

    public function getWhitelistPlayers() : array {
        return $this->plugin->getWhitelist()->get("players");
    }

    public function setWhitelist(bool $status = true) : void {
        $whitelist = $this->plugin->getWhitelist();

        $whitelist->set("status", $status);

        if(!$status)
            $whitelist->set("date", null);

        try {
            $whitelist->save();
        } catch(\JsonException $e) {
            $this->plugin->getLogger()->error($e);
        }
    }

    public function setWhitelistDate(?string $date = null) : void {
        $whitelist = $this->plugin->getWhitelist();
        $whitelist->set("date", $date);
        try {
            $whitelist->save();
        } catch(\JsonException $e) {
            $this->plugin->getLogger()->error($e);
        }
    }

    public function getWhitelistDate() : ?string {
        $date = $this->plugin->getWhitelist()->get("date");
        if(!$date)
            return null;
        return $date;
    }

    public function isWhitelistEnabled() : bool {
        return $this->plugin->getWhitelist()->get("status");
    }

    public function addPlayer(string $nick) : void {
        $nick = strtolower($nick);
        $players = $this->plugin->getWhitelist()->get("players");
        if(in_array($nick, $players))
            return;
        $players[] = $nick;
        $this->plugin->getWhitelist()->set("players", $players);
        try {
            $this->plugin->getWhitelist()->save();
        } catch(\JsonException $e) {
            $this->plugin->getLogger()->error($e);
        }
    }

    public function removePlayer(string $nick) : void {
        $nick = strtolower($nick);
        $players = $this->plugin->getWhitelist()->get("players");
        unset($players[array_search($nick, $players)]);

        $newArray = [];

        foreach($players as $player)
            $newArray[] = $player;

        $this->plugin->getWhitelist()->set("players", $newArray);
        try {
            $this->plugin->getWhitelist()->save();
        } catch(\JsonException $e) {
            $this->plugin->getLogger()->error($e);
        }
    }

    public function isInWhitelist(string $nick) : bool {
        $nick = strtolower($nick);
        $players = self::getWhitelistPlayers();

        if(in_array($nick, $players))
            return true;

        return false;
    }

    public function dateFormat() : string {
        $date = self::getWhitelistDate();

        if($date == null)
            return "§7§lComing Soon...";

        $time = strtotime($date) - time();

        $days = intval(intval($time) / (3600 * 24));
        $hours = (intval($time) / 3600) % 24;
        $minutes = (intval($time) / 60) % 60;
        $seconds = intval($time) % 60;

        if($days < 10)
            $days = "0" . $days;

        if($hours < 10)
            $hours = "0" . $hours;

        if($minutes < 10)
            $minutes = "0" . $minutes;

        if($seconds < 10)
            $seconds = "0" . $seconds;

        return "§e{$days} §7dni §e{$hours} §7godzin §e{$minutes} §7minut §e{$seconds} §7sekund";
    }
}