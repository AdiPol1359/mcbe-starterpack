<?php

declare(strict_types=1);

namespace core\permissions\group;

use core\Main;
use JetBrains\PhpStorm\Pure;

class PlayerGroupManager {

    /** @var GroupPlayer[] */
    private array $players = [];

    public function __construct(private Main $plugin) {
    }

    public function registerPlayer(string $nick) : void {
        $this->players[] = new GroupPlayer($nick, [$this->plugin->getGroupManager()->getDefaultGroup()->getGroupName() => -1], []);
    }

    #[Pure] public function isRegistered(string $nick) : bool {
        foreach($this->players as $groupPlayer) {
            if(strtolower($groupPlayer->getPlayerName()) === strtolower($nick))
                return true;
        }

        return false;
    }

    #[Pure] public function getPlayer(string $nick) : ?GroupPlayer {
        foreach($this->players as $key => $groupPlayer) {
            if(strtolower($groupPlayer->getPlayerName()) === strtolower($nick))
                return $groupPlayer;
        }

        return null;
    }

    public function reload() : void {
        $this->save();
        $this->players = [];
        $this->loadAll();
    }

    /** @var GroupPlayer[] $players */
    public function loadAll() : void {
        $players = [];

        foreach($this->plugin->getProvider()->getQueryResult("SELECT * FROM 'groups'", true) as $row) {
            $playerData = null;

            foreach($players as $groupPlayer) {
                if($groupPlayer->getPlayerName() === $row["nick"])
                    $playerData = $groupPlayer;
            }

            if(!$playerData)
                $playerData = new GroupPlayer($row["nick"], [], []);

            $playerData->addGroup($row["groupName"], (int)$row["expiryDate"]);
            $players[] = $playerData;
        }

        foreach($this->plugin->getProvider()->getQueryResult("SELECT * FROM permissions", true) as $row) {
            $playerData = null;

            foreach($players as $key => $groupPlayer) {
                if($groupPlayer->getPlayerName() === $row["nick"])
                    $playerData = $groupPlayer;
            }

            if(!$playerData)
                continue;

            $playerData->addPermission($row["permission"], (int)$row["expiryDate"]);
        }

        $this->players = $players;
    }

    public function save() : void {
        foreach($this->plugin->getProvider()->getQueryResult("SELECT * FROM 'groups'", true) as $row) {
            $groupPlayer = $this->getPlayer($row["nick"]);
            if(!$groupPlayer)
                continue;

            if(!$groupPlayer->hasGroup($row["groupName"]))
                $this->plugin->getProvider()->executeQuery("DELETE FROM 'groups' WHERE nick = '".$row["nick"]."' AND groupName = '".$row["groupName"]."'");
        }

        foreach($this->plugin->getProvider()->getQueryResult("SELECT * FROM 'permissions'", true) as $row) {
            $groupPlayer = $this->getPlayer($row["nick"]);
            if(!$groupPlayer)
                continue;

            if(!$groupPlayer->hasPermission($row["permission"]))
                $this->plugin->getProvider()->executeQuery("DELETE FROM 'permissions' WHERE nick = '".$row["nick"]."' AND permission = '".$row["groupName"]."'");
        }

        foreach($this->players as $groupPlayer) {
            foreach($groupPlayer->getPlayerGroups() as $groupName => $expiryDate) {
                if(!empty($this->plugin->getProvider()->getQueryResult("SELECT * FROM 'groups' WHERE nick = '".$groupPlayer->getPlayerName()."' AND groupName = '$groupName'", true)))
                    $this->plugin->getProvider()->executeQuery("UPDATE 'groups' SET expiryDate = '$expiryDate' WHERE nick = '".$groupPlayer->getPlayerName()."'");
                else
                    $this->plugin->getProvider()->executeQuery("INSERT INTO 'groups' (nick, groupName, expiryDate) VALUES ('".$groupPlayer->getPlayerName()."', '$groupName', '$expiryDate')");
            }

            foreach($groupPlayer->getPermissions() as $permission => $expiryDate) {
                if(!empty($this->plugin->getProvider()->getQueryResult("SELECT * FROM 'permissions' WHERE nick = '".$groupPlayer->getPlayerName()."' AND permission = '$permission'", true)))
                    $this->plugin->getProvider()->executeQuery("UPDATE 'permissions' SET expiryDate = '$expiryDate' WHERE nick = '".$groupPlayer->getPlayerName()."'");
                else
                    $this->plugin->getProvider()->executeQuery("INSERT INTO 'permissions' (nick, permission, expiryDate) VALUES ('".$groupPlayer->getPlayerName()."', '$permission', '$expiryDate')");
            }
        }
    }

    public function getPlayers() : array {
        return $this->players;
    }
}