<?php

namespace App\Entity;

class GroupPermission
{
    private $id;

    private $permissions;

    private $properties;

    private $group;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPermissions(): ?int
    {
        return $this->permissions;
    }

    public function setPermissions(int $permissions): self
    {
        $this->permissions = $permissions;

        return $this;
    }

    public function getProperties(): ?Properties
    {
        return $this->properties;
    }

    public function setProperties(?Properties $properties): self
    {
        $this->properties = $properties;

        return $this;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): self
    {
        $this->group = $group;

        return $this;
    }
}
