<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Document extends Properties {

    private $id;
    private $name;
    private $creationDate;

    private $versions;

    private $proyect;

    private $template;

    public function __construct()
    {
        $this->versions = new ArrayCollection();
    }

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

    /**
     * @return Collection|Version[]
     */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    public function addVersion(Version $version): self
    {
        if (!$this->versions->contains($version)) {
            $this->versions[] = $version;
            $version->setDocument($this);
        }

        return $this;
    }

    public function removeVersion(Version $version): self
    {
        if ($this->versions->contains($version)) {
            $this->versions->removeElement($version);
            // set the owning side to null (unless already changed)
            if ($version->getDocument() === $this) {
                $version->setDocument(null);
            }
        }

        return $this;
    }

    public function getProyect(): ?Proyect
    {
        return $this->proyect;
    }

    public function setProyect(?Proyect $proyect): self
    {
        $this->proyect = $proyect;

        return $this;
    }

    public function getTemplate(): ?File
    {
        return $this->template;
    }

    public function setTemplate(?File $template): self
    {
        $this->template = $template;

        return $this;
    }

}
