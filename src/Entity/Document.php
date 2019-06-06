<?php

namespace App\Entity;

class Document extends Properties {

    private $id;
    private $name;
    private $creationDate;

    public function getId(): ?string {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self {
        $this->creationDate = $creationDate;

        return $this;
    }

}
