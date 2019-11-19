<?php

namespace App\Entity;

class Setting extends Properties {

    private $id;
    private $entity;
    private $value;

    public function __construct(string $id = null, string $entity = null, $value = null, $owner = null, $group = null, int $ownerPermissions = 07, int $groupPermissions = 0, int $otherPermissions = 0) {
        $this->id = $id;
        $this->entity = $entity;
        $this->value = $value;
        $this->setOwner($owner);
        $this->setGroup($group);
        $this->setPermissions($ownerPermissions, $groupPermissions, $otherPermissions);
        parent::__construct();
    }

    public function getId(): ?string {
        return $this->id;
    }

    public function setId(string $id): self {
        $this->id = $id;

        return $this;
    }

    public function getEntity(): ?string {
        return $this->entity;
    }

    public function setEntity(string $entity): self {
        $this->entity = $entity;

        return $this;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value): self {
        $this->value = $value;
        return $this;
    }

}
