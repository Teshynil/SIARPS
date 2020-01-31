<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project extends Properties {

    /**
     * @var string
     *
     * @ORM\Column(name="c_name", type="string", length=180, nullable=false, unique=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="c_description", type="string", nullable=true)
     */
    private $description;

    /**
     * @var json
     *
     * @ORM\Column(name="c_settings", type="json")
     */
    private $settings = [];

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Document", mappedBy="project", cascade={"persist"})
     * @ORM\OrderBy({
     *     "creationDate"="DESC"
     * })
     */
    private $documents;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true, unique=false)
     */
    private $lastUpdate;
    
    /**
     * @var Document
     *
     * @ORM\ManyToOne(targetEntity="Document", cascade={"persist"})
     */
    private $summary;
    
    public function __construct() {
        $this->documents = new ArrayCollection();
        parent::__construct();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;

        return $this;
    }

    public function getSettings(): ?array {
        return $this->settings;
    }

    public function setSettings(array $settings): self {
        $this->settings = $settings;

        return $this;
    }

    /**
     * @return Collection|Document[]
     */
    public function getDocuments(): Collection {
        return $this->documents;
    }

    public function addDocument(Document $document): self {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->setProject($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self {
        if ($this->documents->contains($document)) {
            $this->documents->removeElement($document);
            // set the owning side to null (unless already changed)
            if ($document->getProject() === $this) {
                $document->setProject(null);
            }
        }

        return $this;
    }

    public function getDocument(string $name):?Document{
        $document=$this->documents->filter(function($document) use($name){
            return $document->getName()==$name;
        });
        if($document->count()==1){
            return $document->first();
        }
        return null;
    }
    
    public function setSummary(Document $summary): self {
        $this->summary = $summary;

        return $this;
    }
    
    public function getSummary(): ?Document {
        return $this->summary;
    }
    
    public function getLastUpdate(): ?DateTime {
        return null;
    }

    public function setLastUpdate(DateTime $date = null): self {
        if ($date == null) {
            $this->lastUpdate = date_create();
        }else{
            $this->lastUpdate = $date;
        }
        return $this;
    }

}
