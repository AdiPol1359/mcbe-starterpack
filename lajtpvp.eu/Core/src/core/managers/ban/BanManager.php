<?php

declare(strict_types=1);

namespace core\managers\ban;

use core\Main;
use JetBrains\PhpStorm\Pure;

class BanManager {

    /** @var BanPlayer[] */
    private array $bans = [];

    public function __construct(private Main $plugin) {}

    public function loadBans() : void {
        foreach ($this->plugin->getProvider()->getQueryResult("SELECT * FROM bans", true) as $data) {
            $this->bans[] = new BanPlayer($data["userName"], $data["address"], $data["deviceId"], $data["admin"], $data["reason"], (int) $data["start"], (int) $data["end"]);
        }
    }

    public function save() : void {
        foreach ($this->plugin->getProvider()->getQueryResult("SELECT * FROM bans", true) as $data) {
            try {
                if(!$this->isBanned($data["nick"])) {
                    $this->plugin->getProvider()->executeQuery("DELETE FROM bans WHERE nick = '" . $data["nick"] . "'");
                }
            } catch(\PDOException $exception) {
                $this->plugin->getLogger()->error("Doszlo do bledu podczas zapisywania bana " . $data["nick"] . " (".$data["deviceId"]."): " . $exception);
            }
        }

        foreach($this->bans as $banPlayer) {
            try {
                if(!empty($db = ($this->plugin->getProvider()->getQueryResult("SELECT * FROM bans WHERE nick = '".$banPlayer->getName()."'", true)))) {
                    if($db["admin"] !== $banPlayer->getAdmin() || $db["ip"] !== $banPlayer->getIp()) {
                        $this->plugin->getProvider()->executeQuery("DELETE FROM bans WHERE nick = '" . $banPlayer->getName() . "'");
                    }
                }

                if(empty($this->plugin->getProvider()->getQueryResult("SELECT * FROM bans WHERE nick = '".$banPlayer->getName()."'", true))) {
                    $this->plugin->getProvider()->executeQuery("INSERT INTO ban (nick, ip, deviceId, admin, reason, start, end) VALUES ('" . $banPlayer->getName() . "', '" . $banPlayer->getIp() . "', '" . $banPlayer->getDeviceId() . "', '" . $banPlayer->getAdmin() . "', '" . $banPlayer->getReason() . "', '" . $banPlayer->getStartBanTime() . "', '" . $banPlayer->getEndBanTime() . "')");
                }
            } catch(\PDOException $exception) {
                $this->plugin->getLogger()->error("Doszlo do bledu podczas zapisywania bana " . $banPlayer->getName() . " (".$banPlayer->getDeviceId()."): " . $exception);
            }
        }
    }

    public function setBan(string $nick, ?string $ip, ?string $deviceId, string $admin, string $reason, int $end, int $start = -1) : void {
        $this->bans[] = new BanPlayer($nick, ($ip === null ? null : base64_encode($ip)), $deviceId, $admin, $reason, ($start === -1 ? time() : $start), $end);
    }

    public function isBanned(string $nick, ?string $ip = null, ?string $deviceId = null) : bool {

        foreach($this->bans as $key => $banPlayer) {

            if($banPlayer->getEndBanTime() <= time()) {
                unset($this->bans[$key]);
                continue;
            }

            if($banPlayer->getName() === $nick)
                return true;

            if($ip) {
                if($banPlayer->getDecodedIp() === $ip)
                    return true;
            }

            if($deviceId) {
                if($banPlayer->getDeviceId() === $deviceId)
                    return true;
            }
        }

        return false;
    }

    #[Pure] public function getBanNickInfo(string $nick) : ?BanPlayer {
        foreach($this->bans as $banPlayer) {
            if($banPlayer->getName() === $nick)
                return $banPlayer;
        }

        return null;
    }

    public function getBanInfo(string $nick, ?string $ip = null, ?string $deviceId = null) : ?BanPlayer {
        foreach($this->bans as $key => $banPlayer) {

            if($banPlayer->getEndBanTime() <= time()) {
                unset($this->bans[$key]);
                continue;
            }

            if($banPlayer->getName() === $nick)
                return $banPlayer;

            if($ip) {
                if($banPlayer->getDecodedIp() === $ip)
                    return $banPlayer;
            }

            if($deviceId) {
                if($banPlayer->getDeviceId() === $deviceId)
                    return $banPlayer;
            }
        }

        return null;
    }

    public function unBanNick(string $nick) : void {
        foreach($this->bans as $key => $banPlayer) {
            if($nick === $banPlayer->getName())
                unset($this->bans[$key]);
        }
    }

    public function unBanIp(string $ip) : void {
        foreach($this->bans as $key => $banPlayer) {
            if($ip === $banPlayer->getDecodedIp())
                unset($this->bans[$key]);
        }
    }

    #[Pure] public function getBanFormat(BanPlayer $banPlayer) : string {
        return "§8<§7==========§8[§l§e ZOSTALES ZBANOWANY §r§8]§7==========§8>".
            "\n\n".str_repeat(" ", 20)."§r§7Powod§8: §e".$banPlayer->getReason().
            "\n".str_repeat(" ", 20)."§r§7Przez§8: §e".$banPlayer->getAdmin().
            "\n".str_repeat(" ", 20)."§r§7Wygasa§8: §e".date("d.m.Y H:i:s", $banPlayer->getEndBanTime());
    }
}