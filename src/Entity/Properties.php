<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

class Properties {

    protected $owner;
    protected $group;
    protected $ownerPermissions;
    protected $otherPermissions;
    protected $groupsPermissions;

    public function getOwnerPermissions(): ?int {
        return $this->ownerPermissions;
    }

    public function setOwnerPermissions(int $ownerPermissions): self {
        $this->ownerPermissions = $ownerPermissions;

        return $this;
    }

    public function getOtherPermissions(): ?int {
        return $this->otherPermissions;
    }

    public function setOtherPermissions(int $otherPermissions): self {
        $this->otherPermissions = $otherPermissions;

        return $this;
    }

    public function getGroupsPermissions(): ?array {
        return $this->groupsPermissions;
    }

    public function getOwnGroupPermissions(): ?int {
        return $this->groupsPermissions[0];
    }

    public function getOthersGroupPermissions(): ?array {
        $tmp = $this->groupsPermissions;
        unset($tmp[0]);
        return $tmp;
    }

    public function setGroupsPermissions(array $groupsPermissions): self {
        $this->groupsPermissions = $groupsPermissions;

        return $this;
    }

    public function addGroupsPermissions(string $groupId, int $permission): self {
        $this->groupsPermissions[$groupId] = $permission;
        return $this;
    }

    public function removeGroupsPermissions(string $groupId): self {
        if (isset($this->groupsPermissions[$groupId])) {
            unset($this->groupsPermissions[$groupId]);
        }
        return $this;
    }

    public function getOwner(): ?User {
        return $this->owner;
    }

    public function setOwner(?User $owner): self {
        $this->owner = $owner;

        return $this;
    }

    public function getGroup(): ?Group {
        return $this->group;
    }

    public function setGroup(?Group $group): self {
        $this->group = $group;

        return $this;
    }

    public function setPermissions(int $owner, int $group, int $other) {
        $this->setOwnerPermissions($owner);
        $this->addGroupsPermissions(0, $group);
        $this->setOtherPermissions($other);
        return $this;
    }

}
