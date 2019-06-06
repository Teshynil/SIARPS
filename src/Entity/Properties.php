<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

class Properties {

    protected $owner;
    protected $group;
    protected $permissions;

    public function getPermissions(): ?int {
        return $this->permissions;
    }

    public function setPermissions(int $permissions): self {
        $this->permissions = $permissions;

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

}
