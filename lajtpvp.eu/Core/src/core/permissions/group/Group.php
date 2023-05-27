<?php

declare(strict_types=1);

namespace core\permissions\group;

class Group {

    private string $groupName;

    private int $rank;

    private bool $default;

    private array $parents;
    private array $permissions;

    private string $format;
    private string $nameTag;

    public function __construct(string $groupName, int $rank, bool $default, array $parents, array $permissions, string $format, string $nameTag) {
        $this->groupName = $groupName;
        $this->rank = $rank;
        $this->default = $default;
        $this->parents = $parents;
        $this->permissions = $permissions;
        $this->format = $format;
        $this->nameTag = $nameTag;
    }

    public function getGroupName() : string {
        return $this->groupName;
    }

    public function getRank() : int {
        return $this->rank;
    }

    public function isDefault() : bool {
        return $this->default;
    }

    public function getParents() : array {
        return $this->parents;
    }

    public function getPermissions() : array {
        return $this->permissions;
    }

    public function getFormat() : string {
        return $this->format;
    }

    public function getNameTag() : string {
        return $this->nameTag;
    }

    public function hasPermission(string $permission) : bool {
        foreach($this->permissions as $key => $groupPermission) {
            if($groupPermission === $permission)
                return true;
        }

        return false;
    }
}