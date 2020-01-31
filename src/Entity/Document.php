<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Document
 *
 * @ORM\Entity(repositoryClass="App\Repository\DocumentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Document extends Properties {

    /**
     * @var string
     *
     * @ORM\Column(name="c_name", type="string", length=180, nullable=false)
     */
    private $name;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="c_creation_date", type="datetime")
     */
    private $creationDate;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Version", mappedBy="document", cascade={"persist"})
     * @ORM\OrderBy({
     *     "date"="DESC"
     * })
     */
    private $versions;

    /**
     * @var \App\Entity\Project
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="documents")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="t_project_id", referencedColumnName="id")
     * })
     */
    private $project;

    /**
     * @var \App\Entity\Template
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Template")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="t_template_id", referencedColumnName="id")
     * })
     */
    private $template;

    public function __construct()
    {
        $this->versions = new ArrayCollection();
        parent::__construct();
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
    
    /**
     * @ORM\PrePersist
     */
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
    
    public function getCurrent(): ?Version
    {
        return $this->versions->get(0);
    }

    public function getCurrentLocked(): ?Version
    {
        foreach ($this->versions->getValues() as $version) {
            if($version->getLockState()){
                return $version;
            }
        }
        return null;
    }
}
