<?php

namespace core\task\tasks;

use core\manager\managers\StatsManager;
use core\task\AsyncQuery;
use Exception;

class MySQLSaveAsyncTask extends AsyncQuery {

    private array $users;
    private int $caves;

    public function __construct(array $users, int $caves){
        $this->users = $users;
        $this->caves = $caves;
    }

    public function onRun() {

        try {
            $mysqli = $this->getMysqli();

            if($mysqli->connect_error)
                return;

            $stats = [];

            $mysqli->query("DELETE FROM topCobblestone");
            $mysqli->query("DELETE FROM topMoney");
            $mysqli->query("DELETE FROM topKills");
            $mysqli->query("DELETE FROM topDeaths");
            $mysqli->query("DELETE FROM topSpendTime");
            $mysqli->query("DELETE FROM topAssists");
            $mysqli->query("DELETE FROM topMadeQuests");

            for($i = 0; $i < count($this->users); $i++) {

                if(!$this->users[$i])
                    return;

                $stats["topCobblestone"][$this->users[$i]->getName()] = $this->users[$i]->getCobble();
                $stats["topMoney"][$this->users[$i]->getName()] = $this->users[$i]->getPlayerMoney();

                $stats["topKills"][$this->users[$i]->getName()] = $this->users[$i]->getStat(StatsManager::KILLS);
                $stats["topDeaths"][$this->users[$i]->getName()] = $this->users[$i]->getStat(StatsManager::DEATHS);
                $stats["topAssists"][$this->users[$i]->getName()] = $this->users[$i]->getStat(StatsManager::ASSISTS);
                $stats["topSpendTime"][$this->users[$i]->getName()] = $this->users[$i]->getStat(StatsManager::TIME_PLAYED);
                $stats["topMadeQuests"][$this->users[$i]->getName()] = $this->users[$i]->getDoneQuestCount();
            }

            foreach($stats as $key => $data) {
                asort($stats[$key], SORT_NUMERIC);
                $stats[$key] = array_reverse(array_slice($stats[$key], -10, 10, true), true);
            }

            foreach($stats as $statName => $data) {
                foreach($data as $dataName => $dataNum)
                    $mysqli->query("INSERT INTO ".$statName." (nick, count) VALUES ('$dataName', '$dataNum')");
            }

            $users = count($this->users);
            $caves = $this->caves;

            if(!empty($mysqli->query("SELECT * FROM serverInfo")->fetch_array())) {
                $mysqli->query("UPDATE serverInfo SET `registerPlayers` = '$users'");
                $mysqli->query("UPDATE serverInfo SET `registerCaves` = '$caves'");
            } else
                $mysqli->query("INSERT INTO serverInfo (registerPlayers, registerCaves) VALUES ('$users', '$caves')");
        }catch(Exception $err){}
    }
}