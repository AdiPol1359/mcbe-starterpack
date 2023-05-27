<?php

declare(strict_types=1);

namespace core\permissions\group;

use core\Main;
use JetBrains\PhpStorm\Pure;

class GroupManager {

    /** @var Group[] */
    private array $groups;

    public function __construct(private Main $plugin) {}

    public function load() : void {
        foreach($this->plugin->getGroup()->getAll()["groups"] as $groupName => $groupData)
            $this->groups[] = new Group($groupName, $groupData["rank"], $groupData["default"], $groupData["parents"], $groupData["permissions"], $groupData["format"], $groupData["nameTag"]);
    }

    public function reload() : void {
        $this->groups = [];
        $this->load();
    }

    #[Pure] public function getDefaultGroup() : ?Group {
        foreach($this->groups as $group) {
            if($group->isDefault())
                return $group;
        }

        return null;
    }

    #[Pure] public function getGroupByName(string $groupName) : Group {
        foreach($this->groups as $group) {
            if($group->getGroupName() === $groupName)
                return $group;
        }

        return self::getDefaultGroup();
    }

    #[Pure] public function getGroupsUnderRank(int $rank) : array {
        $groups = [];

        foreach($this->groups as $group) {
            if($group->getRank() <= $rank)
                $groups[] = $group;
        }

        return $groups;
    }

    public function getGroups() : array {
        return $this->groups;
    }
}