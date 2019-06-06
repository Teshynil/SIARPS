<?php

namespace App\Entity;

class Setting extends Properties {

    private $id;
    private $entity;
    private $value;

    public function __construct(string $id = null, bool $entity = false, $value = null, $owner = null, $group = null, $permissions = 0) {
        $this->id = $id;
        $this->entity = $entity;
        $this->value = $value;
        $this->setOwner($owner);
        $this->setGroup($group);
        $this->setPermissions($permissions);
    }

    public function getId(): ?string {
        return $this->id;
    }

    public function setId(string $id): self {
        $this->id = $id;

        return $this;
    }

    public function getEntity(): ?bool {
        return $this->entity;
    }

    public function setEntity(bool $entity): self {
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
