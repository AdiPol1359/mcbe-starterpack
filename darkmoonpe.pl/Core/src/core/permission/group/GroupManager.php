<?php

namespace core\permission\group;

use core\permission\provider\Provider;
use core\permission\provider\SQLite3Provider;
use pocketmine\{
    Server,
    Player,
};
use pocketmine\utils\Config;
use core\Main;

class GroupManager {

    private SQLite3Provider $provider;
    private array $players = [];

    public static function expiryFormat(?int $time = null) : ?array {
        if($time == null)
            return null;

        $days = intval(intval($time) / (3600 * 24));
        $hours = (intval($time) / 3600) % 24;
        $minutes = (intval($time) / 60) % 60;
        $seconds = intval($time) % 60;

        if($hours < 10)
            $hours = "0" . $hours;

        if($minutes < 10)
            $minutes = "0" . $minutes;

        if($seconds < 10)
            $seconds = "0" . $seconds;

        return [
            "seconds" => $seconds,
            "minutes" => $minutes,
            "hours" => $hours,
            "days" => $days
        ];
    }

    public function __construct() {
        $this->provider = new SQLite3Provider();
    }

    public function getProvider() : Provider {
        return $this->provider;
    }

    public function getConfig() : Config {
        return Main::getGroup();
    }

    public function getPlayer(string $playerName) : PlayerGroupManager {
        $p = Server::getInstance()->getPlayer($playerName);
        if($p instanceof Player)
            $player = Server::getInstance()->getPlayer($playerName);
        else
            $player = Server::getInstance()->getOfflinePlayer($playerName);

        if($player instanceof Player && isset($this->players[$player->getName()]))
            return $this->players[$player->getName()];

        return new PlayerGroupManager($player, $this->provider);
    }

    public function registerPlayer(Player $player) : void {
        if(!isset($this->players[$player->getName()]))
            $this->players[$player->getName()] = new PlayerGroupManager($player, $this->provider);

        $this->players[$player->getName()]->updatePermissions();
    }

    public function unregisterPlayer(Player $player) : void {
        if(isset($this->players[$player->getName()])) {
            $player->removeAttachment($this->getPlayer($player->getName())->getAttachment());
            unset($this->players[$player->getName()]);
        }
    }

    public function getDefaultGroup() : ?Group {
        foreach(Main::getGroup()->get("groups") as $groupName => $groupData)
            if(isset($groupData['default']) && $groupData['default'])
                return $this->getGroup($groupName);

        return null;
    }

    public function setDefaultGroup(Group $defGroup) : void {
        foreach($this->getAllGroups() as $group) {
            $groupData = $group->getData();

            if(isset($groupData['default']) && $groupData['default'])
                if($group->getName() != $defGroup->getName())
                    $groupData['default'] = false;

            if($group->getName() == $defGroup->getName())
                $groupData['default'] = true;

            $group->setData($groupData);
        }
    }

    public function getGroup(string $groupName) : ?Group {
        if(!isset(Main::getGroup()->get("groups")[$groupName]))
            return null;

        return new Group($groupName, $this->provider, Main::getGroup()->get("groups")[$groupName]);
    }

    // RETURN RANKS FROM HIGHEST TO LOWEST HIERARCHY
    public function getAllGroups() : array {
        $groups = [];

        foreach(Main::getGroup()->get("groups") as $groupName => $groupData)
            $groups[] = $this->getGroup($groupName);

        $groupsHierarchy = [];

        foreach($groups as $group) {
            $rank = $group->getRank() == null ? 0 : $group->getRank();
            $groupsHierarchy[$rank][] = $group;
        }

        krsort($groupsHierarchy);

        $groups = [];

        foreach($groupsHierarchy as $grps)
            foreach($grps as $group)
                $groups[] = $group;

        return $groups;
    }

    public function getAllUsers() : array {
        $users = [];

        foreach($this->provider->getAllUsers() as $nick)
            $users[] = $this->getPlayer($nick);

        return $users;
    }

    public function isGroupExists(string $groupName) : bool {
        return $this->getGroup($groupName) !== null;
    }

    public function userExists(string $userName) : bool {
        return $this->provider->userExists($userName);
    }

    public function createGroup(string $groupName, array $parents = []) : void {
        $groups = Main::getGroup()->get("groups");
        $groups[$groupName] = [
            "rank" => 0,
            "parents" => $parents,
            "permissions" => []
        ];

        Main::getGroup()->set("groups", $groups);
        Main::getGroup()->save();
    }

    public function reload() : void {
        Main::setGroup(new Config(Main::getInstance()->getDataFolder() . 'data/groups.yml', Config::YAML));
    }
}