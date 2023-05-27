<?php

namespace core\managers\war;

use core\Main;
use core\utils\Settings;
use JetBrains\PhpStorm\Pure;

class WarManager {

    /** @var War[] */
    private array $wars = [];

    public function __construct(private Main $plugin) {
        $this->loadWars();
    }

    public function createWar(string $attacker, string $attacked) : void {
        $attackerStats = [
            "tag" => $attacker,
            "kills" => 0,
            "deaths" => 0
        ];

        $attackedStats = [
            "tag" => $attacked,
            "kills" => 0,
            "deaths" => 0
        ];

        $day = date("d") + (date("H") >= 18 ? 1 : 0);
        $date = date("Y-m-".$day." ".Settings::$TNT_START.":00:00", time());
        $endDate = date("Y-m-".$day." ".Settings::$TNT_END.":00:00", time());

        $this->wars[] = new War($this->getHighestId(), $attackerStats, $attackedStats, strtotime($date), strtotime($endDate), false, "");
    }

    public function save() : void {
        $provider = $this->plugin->getProvider();

        foreach($this->wars as $war) {
            $attackerGuild = json_encode($war->serializeAttacker());
            $attackedGuild = json_encode($war->serializeAttacked());

            if(empty($provider->getQueryResult("SELECT * FROM wars WHERE id = '".$war->getId()."'", true))) {
                $provider->executeQuery("INSERT INTO wars (id, attackerGuild, attackedGuild, startTime, endTime, ended, winnerGuild) VALUES ('" . $war->getId() . "', '" . $attackerGuild . "', '" . $attackedGuild . "', '" . $war->getStartTime() . "', '" . $war->getEndTime() . "', '" . $war->hasEnded() . "', '" . $war->getWinner() . "')");
            } else {
                $provider->executeQuery("UPDATE wars SET attackerGuild = '" . $attackerGuild . "', attackedGuild = '" . $attackedGuild . "', startTime = '" . $war->getStartTime() . "', endTime = '" . $war->getEndTime() . "', ended = '" . $war->hasEnded() . "', winnerGuild = '" . $war->getWinner() . "' WHERE id = '" . $war->getId() . "'");
            }
        }
    }

    public function loadWars() : void {
        $provider = $this->plugin->getProvider();

        foreach($provider->getQueryResult("SELECT * FROM wars", true) as $row) {
            $this->wars[] = new War($row["id"], json_decode($row["attackerGuild"], true), json_decode($row["attackedGuild"], true), $row["startTime"], $row["endTime"], $row["ended"], $row["winnerGuild"]);
        }
    }

    public function getWar(string $tag) : ?War {

        foreach($this->wars as $war) {
            if($war->hasEnded())
                continue;

            if($war->getAttacker() === $tag || $war->getAttacked() === $tag)
                return $war;
        }

        return null;
    }

    #[Pure] public function getWarById(int $id) : ?War {
        foreach($this->wars as $war) {
            if($war->getId() === $id)
                return $war;
        }

        return null;
    }

    public function getHighestId() : int {
        $id = 0;

        foreach($this->wars as $war) {
            if($war->getId() >= $id)
                $id = $war->getId() + 1;
        }

        $highestDbId = $this->plugin->getProvider()->getQueryResult("SELECT * FROM wars ORDER BY id DESC LIMIT 0, 1", true)["id"] ?? 1;
        if($highestDbId >= $id)
            $id = $highestDbId + 1;

        return $id;
    }

    public function getWars() : array {
        return $this->wars;
    }

    #[Pure] public function getEndedWars() : array {
        $wars = [];

        foreach($this->wars as $war) {
            if($war->hasEnded())
                $wars[] = $war;
        }

        return $wars;
    }
}