<?php

namespace core\permission\group;

use core\permission\managers\NameTagManager;
use pocketmine\{
    OfflinePlayer,
    Server,
    Player,
    IPlayer,
};
use pocketmine\permission\PermissionAttachment;
use core\Main;
use core\permission\provider\Provider;

class PlayerGroupManager {

    private IPlayer $player;
    private ?PermissionAttachment $attachment = null;
    private Provider $provider;

    public function __construct(IPlayer $player, Provider $provider) {
        $this->player = $player;
        $this->provider = $provider;
        $this->init();
    }

    private function init() : void {
        if($this->player instanceof Player)
            $this->attachment = $this->player->addAttachment(Main::getInstance());
    }

    public function getAttachment() : PermissionAttachment {
        return $this->attachment;
    }

    public function getPlayer() : IPlayer {
        return $this->player;
    }

    public function getGroups() : array {
        return $this->provider->getPlayerGroups($this->player);
    }

    public function addGroup(Group $group, ?int $time = null, ?string $levelName = null) : void {
        if($this->hasGroup($group, false))
            $this->removeGroup($group, false);
        if($time != null) {
            $date = date('d.m.Y H:i:s', strtotime(date("H:i:s")) + $time);
            $this->provider->addPlayerGroup($this->player, $group, $date, $levelName);
        } else
            $this->provider->addPlayerGroup($this->player, $group, null, $levelName);

        $this->updatePermissions();

        if($this->player instanceof Player)
            NameTagManager::updateNameTag($this->player);
    }

    public function addDefaultGroup() : void {
        $defaultGroup = Main::getGroupManager()->getDefaultGroup();
        $this->addGroup($defaultGroup);
    }

    public function setGroup(Group $group, ?int $time = null, ?string $levelName = null) : void {
        $this->removeGroups();
        $this->addGroup($group, $time, $levelName);
    }

    public function removeGroup(Group $group, bool $addDefault = true) : void {
        $this->provider->removePlayerGroup($this->player, $group);

        if($addDefault && $this->getGroup() == null)
            $this->addDefaultGroup();

        $this->updatePermissions();
    }

    public function removeGroups() : void {
        $this->provider->removePlayerGroups($this->player);
        $this->updatePermissions();
    }

    public function hasGroup(?Group $group = null, bool $checkLevel = true) : bool {
        return $this->provider->hasPlayerGroup($this->player, $group, $checkLevel);
    }

    public function getGroup() : ?Group {
        $groups = [];

        foreach(Main::getGroupManager()->getAllGroups() as $group)
            if($this->hasGroup($group)) {
                $rank = $group->getRank() == null ? 0 : $group->getRank();
                $groups[$rank][] = $group;
            }

        if(empty($groups))
            return null;

        return $groups[max(array_keys($groups))][0];
    }

    public function getGroupExpiry(Group $group) : ?int {
        $date = $this->provider->getPlayerGroupExpiryDate($this->player, $group);

        if($date == null)
            return null;

        return strtotime($date) - time();
    }

    public function getPermissions() : array {
        $permissions = [];

        foreach($this->getGroups() as $group) {
            foreach($group->getPermissions() as $permissionName) {
                $permission = Server::getInstance()->getPluginManager()->getPermission($permissionName);

                if($permissionName == '*') {
                    if(!in_array($permissionName, $permissions))
                        $permissions[] = $permissionName;

                    foreach(Server::getInstance()->getPluginManager()->getPermissions() as $perm)
                        if(!in_array($perm->getName(), $permissions))
                            $permissions[] = $perm->getName();

                    // PERMISSION.*
                } elseif(substr($permissionName, -1) == '*') {
                    if(!in_array($permissionName, $permissions))
                        $permissions[] = $permissionName;
                    foreach(Server::getInstance()->getPluginManager()->getPermissions() as $perm)
                        if(substr($perm->getName(), 0, strlen($permissionName) - 1) == substr($permissionName, 0, strlen($permissionName) - 1))
                            $permissions[] = $perm->getName();
                } else {
                    if(!in_array($permissionName, $permissions))
                        $permissions[] = $permissionName;

                    if($permission == null)
                        continue;

                    foreach($permission->getChildren() as $childPerm => $value)
                        if(!in_array($childPerm, $permissions))
                            $permissions[] = $childPerm;
                }
            }
        }

        foreach($this->provider->getPlayerPermissions($this->player) as $permissionName) {
            $permission = Server::getInstance()->getPluginManager()->getPermission($permissionName);
            if($permissionName == '*') {
                foreach(Server::getInstance()->getPluginManager()->getPermissions() as $perm)
                    if(!in_array($perm->getName(), $permissions))
                        $permissions[] = $perm->getName();

                // PERMISSSION.*
            } elseif(substr($permissionName, -1) == '*') {
                if(!in_array($permissionName, $permissions))
                    $permissions[] = $permissionName;
                foreach(Server::getInstance()->getPluginManager()->getPermissions() as $perm)
                    if(substr($perm->getName(), 0, strlen($permissionName) - 1) == substr($permissionName, 0, strlen($permissionName) - 1))
                        $permissions[] = $perm->getName();
            } else {
                if(!in_array($permissionName, $permissions))
                    $permissions[] = $permissionName;

                if($permission == null)
                    continue;

                foreach($permission->getChildren() as $childPerm => $value)
                    if(!in_array($childPerm, $permissions))
                        $permissions[] = $childPerm;
            }
        }

        return $permissions;
    }

    public function addPermission(string $permission, ?int $time = null) : void {
        if($this->hasPermission($permission))
            $this->removePermission($permission);

        if($time != null) {
            $date = date('d.m.Y H:i:s', strtotime(date("H:i:s")) + $time);
            $this->provider->addPlayerPermission($this->player, $permission, $date);
        } else
            $this->provider->addPlayerPermission($this->player, $permission);

        $this->updatePermissions();
    }

    public function removePermission(string $permission) : void {
        $this->provider->removePlayerPermission($this->player, $permission);
        $this->updatePermissions();
    }

    public function hasPermission(string $permission) : bool {
        return $this->provider->hasPlayerPermission($this->player, $permission);
    }

    public function delete() : void {
        $this->provider->deleteUser($this->player);
        $this->updatePermissions();
    }

    public function updatePermissions() : void {
        $player = $this->player;

        if($player instanceof OfflinePlayer)
            return;

        $permissions = [];

        foreach($this->getPermissions() as $permission)
            $permissions[$permission] = true;

        $this->attachment->clearPermissions();
        $this->attachment->setPermissions($permissions);
    }
}