<?php

declare(strict_types=1);

namespace core\tasks\async;

use core\Main;
use core\permissions\group\Group;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;

class PermissionAsyncTask extends AsyncTask {

    private string $playerName;

    private ?Group $defaultGroup;
    private array $groups;
    private array $playerGroups;
    private array $permissions;
    private array $resultPermissions;

    public function __construct(string $playerName, ?Group $defaultGroup, array $groups, array $playerGroups, array $permissions) {
        $this->playerName = $playerName;
        $this->defaultGroup = $defaultGroup;
        $this->groups = $groups;
        $this->playerGroups = $playerGroups;
        $this->permissions = $permissions;
    }

    public function onRun() : void {
        $permissions = [];

        foreach($this->playerGroups as $groupName => $expiryDate) {
            $group = null;

            foreach($this->groups as $defGroup) {
                if($defGroup->getGroupName() === $groupName)
                    $group = $defGroup;
            }

            if($group === null)
                continue;

            foreach($group->getPermissions() as $permission)
                $permissions[$permission] = true;
        }

        foreach($this->permissions as $permission => $expiryDate)
            $permissions[$permission] = true;

        $this->resultPermissions = $permissions;
    }

    public function onCompletion() : void {
        if(!(Server::getInstance()->getPlayerExact($this->playerName))) {
            return;
        }

        if(!($playerGroup = Main::getInstance()->getPlayerGroupManager()->getPlayer($this->playerName)) || !$playerGroup->getAttachment()) {
            return;
        }

        $playerGroup->getAttachment()->clearPermissions();
        $playerGroup->getAttachment()->setPermissions($this->resultPermissions);
    }
}