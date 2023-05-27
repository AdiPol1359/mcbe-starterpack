<?php

declare(strict_types=1);

namespace core\managers\mute;

use core\Main;
use core\utils\TimeUtil;
use JetBrains\PhpStorm\Pure;

class MuteManager {

    /** @var MutePlayer[] */
    private array $mutes = [];

    public function __construct(private Main $plugin) {}

    public function loadMutes() : void {
        foreach($this->plugin->getProvider()->getQueryResult("SELECT * FROM mute", true) as $row) {
            $this->mutes[] = new MutePlayer($row["nick"],$row["admin"], $row["reason"], (int)$row["start"], (int)$row["end"]);
        }
    }

    public function save() : void {
        foreach($this->plugin->getProvider()->getQueryResult("SELECT * FROM mute", true) as $row) {
            if(!$this->isMuted($row["nick"])) {
                $this->plugin->getProvider()->executeQuery("DELETE FROM mute WHERE nick = '" . $row["nick"] . "'");
            }
        }

        foreach($this->mutes as $mutePlayer) {
            if(!empty($db = ($this->plugin->getProvider()->getQueryResult("SELECT * FROM mute WHERE nick = '" . $mutePlayer->getName() . "'", true)))) {
                if($db["admin"] !== $mutePlayer->getAdmin()) {
                    $this->plugin->getProvider()->executeQuery("DELETE FROM mute WHERE nick = '" . $mutePlayer->getName() . "'");
                }
            }

            if(empty($this->plugin->getProvider()->getQueryResult("SELECT * FROM mute WHERE nick = '" . $mutePlayer->getName() . "'", true))) {
                $this->plugin->getProvider()->executeQuery("INSERT INTO mute (nick, admin, reason, start, end) VALUES ('" . $mutePlayer->getName() . "', '" . $mutePlayer->getAdmin() . "', '" . $mutePlayer->getReason() . "', '" . $mutePlayer->getStartMuteTime() . "', '" . $mutePlayer->getEndMuteTime() . "')");
            }
        }
    }

    public function setMute(string $nick, string $admin, string $reason, int $end, int $start = -1) : void {
        $this->mutes[] = new MutePlayer($nick, $admin, $reason, ($start === -1 ? time() : $start), $end);
    }

    public function isMuted(string $nick) : bool {

        foreach($this->mutes as $key => $mutePlayer) {

            if($mutePlayer->getEndMuteTime() <= time()) {
                unset($this->mutes[$key]);
                continue;
            }

            if($mutePlayer->getName() === $nick)
                return true;
        }

        return false;
    }

    #[Pure] public function getMuteNickInfo(string $nick) : ?MutePlayer {
        foreach($this->mutes as $mutePlayer) {
            if($mutePlayer->getName() === $nick)
                return $mutePlayer;
        }

        return null;
    }

    public function unMuteNick(string $nick) : void {
        foreach($this->mutes as $key => $mutePlayer) {
            if($nick === $mutePlayer->getName())
                unset($this->mutes[$key]);
        }
    }

    public function getMuteFormat(MutePlayer $mutePlayer) : array {
        return ["§r§7Powod§8: §e".$mutePlayer->getReason(),
                "§r§7Przez§8: §e".$mutePlayer->getAdmin(),
                "§r§7Wygasa za§8: §e".TimeUtil::convertIntToStringTime(($mutePlayer->getEndMuteTime() - time()), "§e", "§7", true, false)];
    }
}