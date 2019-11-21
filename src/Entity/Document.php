<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Document extends Properties {

    private $id;
    private $name;
    private $creationDate;

    private $versions;

    private $project;

    private $template;

    public function __construct()
    {
        $this->versions = new ArrayCollection();
        parent::__construct();
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
    
    public function updateCreationDate(){
        $this->creationDate = new \DateTime("now");
    }
    
    public function setCreationDate(?\DateTimeInterface $creationDate=null): self {
        if($creationDate==null){
            $creationDate= new \DateTime("now");
        }
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

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function setTemplate(?Template $template): self
    {
        $this->template = $template;

        return $this;
    }

}
