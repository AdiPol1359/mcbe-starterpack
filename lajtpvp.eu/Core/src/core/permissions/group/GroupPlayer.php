<?php

declare(strict_types=1);

namespace core\permissions\group;

use core\Main;
use core\tasks\async\PermissionAsyncTask;
use JetBrains\PhpStorm\Pure;
use pocketmine\permission\PermissionAttachment;
use pocketmine\Server;

class GroupPlayer {

    private string $playerName;
    private array $playerGroups;
    private array $permissions;

    private ?PermissionAttachment $attachment = null;

    public function __construct(string $playerName, array $playerGroup, array $permissions) {
        $this->playerName = $playerName;
        $this->playerGroups = $playerGroup;
        $this->permissions = $permissions;

        $this->joinPlayer();
    }

    public function getPlayerName() : string {
        return $this->playerName;
    }

    public function getPlayerGroups() : array {
        return $this->playerGroups;
    }

    public function getPlayerPermissions() : array {
        return $this->permissions;
    }

    #[Pure] public function getGroup() : ?Group {
        $group = null;

        foreach($this->playerGroups as $groupName => $expiryTime) {
            if(!($mainGroup = Main::getInstance()->getGroupManager()->getGroupByName($groupName)))
                continue;

            if(!$group) {
                $group = $mainGroup;
                continue;
            }

            if($mainGroup->getRank() > $group->getRank())
                $group = $mainGroup;
        }

        return $group;
    }

    public function hasGroup(string $group) : bool {
        foreach($this->playerGroups as $groupName => $expiryTime) {
            if($groupName === $group)
                return true;
        }

        return false;
    }

    public function hasPermission(string $permissionName) : bool {
        foreach($this->permissions as $permission => $expiryDate) {
            if($permission === $permissionName)
                return true;
        }

        return false;
    }

    public function getGroupExpire(string $group) : int {
        foreach($this->playerGroups as $groupName => $expiryTime) {
            if($groupName === $group)
                return $expiryTime;
        }

        return -1;
    }

    public function getPermissions() : array {
        return $this->permissions;
    }

    #[Pure] public function getAllPermissions() : array {
        $permissions = [];

        foreach($this->playerGroups as $groupName => $expire) {
            $group = Main::getInstance()->getGroupManager()->getGroupByName($groupName);

            foreach($group->getPermissions() as $indexPermission => $perm) {
                $permissions[$perm] = -1;
            }
        }

        foreach($this->permissions as $pPermission => $pPermissionExpire) {
            if(isset($permissions[$pPermission])) {
                continue;
            }

            $permissions[$pPermission] = $pPermissionExpire;
        }

        return $permissions;
    }

    public function setGroup(string $groupName) : void {
        $this->playerGroups = [];
        $this->playerGroups[$groupName] = -1;
        $this->updatePermissions();
    }

    public function addGroup(string $groupName, int $expiryDate = -1) : void {
        $this->playerGroups[$groupName] = $expiryDate;
        $this->updatePermissions();
    }

    public function addPermission(string $permissionName, int $expiryDate = -1) : void {
        $this->permissions[$permissionName] = $expiryDate;
        $this->updatePermissions();
    }

    public function removePermission(string $permissionName) : void {
        unset($this->permissions[$permissionName]);
        $this->updatePermissions();
    }

    public function removeGroup(string $groupName) : void {
        unset($this->playerGroups[$groupName]);
        $this->updatePermissions();
    }

    public function joinPlayer() : void {
        if(($player = Server::getInstance()->getPlayerExact($this->playerName))) {
            $this->attachment = $player->addAttachment(Main::getInstance());
        }

        $this->updatePermissions();
    }

    public function quitPlayer() : void {
        if($this->attachment === null) {
            return;
        }

        if(($player = Server::getInstance()->getPlayerExact($this->playerName))) {
            $player->removeAttachment($this->attachment);
        }
    }

    public function getAttachment() : ?PermissionAttachment {
        return $this->attachment;
    }

    public function updatePermissions() : void {
        Server::getInstance()->getAsyncPool()->submitTask(new PermissionAsyncTask($this->playerName, Main::getInstance()->getGroupManager()->getDefaultGroup(), Main::getInstance()->getGroupManager()->getGroups(), $this->playerGroups, $this->permissions));
    }
}