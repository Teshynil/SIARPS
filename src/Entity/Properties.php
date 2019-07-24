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

    public function __construct() {
        $this->groupsPermissions = new ArrayCollection();
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

    /**
     * @return Collection|GroupPermission[]
     */
    public function getGroupsPermissions(): Collection {
        return $this->groupsPermissions;
    }

    public function addGroupsPermission(GroupPermission $groupsPermission): self {
        if (!$this->groupsPermissions->contains($groupsPermission)) {
            $this->groupsPermissions[] = $groupsPermission;
            $groupsPermission->setProperties($this);
        }

        return $this;
    }

    public function removeGroupsPermission(GroupPermission $groupsPermission): self {
        if ($this->groupsPermissions->contains($groupsPermission)) {
            $this->groupsPermissions->removeElement($groupsPermission);
            // set the owning side to null (unless already changed)
            if ($groupsPermission->getProperties() === $this) {
                $groupsPermission->setProperties(null);
            }
        }

        return $this;
    }

}
