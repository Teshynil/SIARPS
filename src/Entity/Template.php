<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\File;

/**
 * Template
 *
 * @ORM\Entity(repositoryClass="App\Repository\TemplateRepository")
 */
class Template extends Properties 
{
    
    /**
     * @var string
     *
     * @ORM\Column(name="c_name", type="string", length=180, nullable=false, unique=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="c_type", type="string")
     */
    private $type;

    /**
     * @var json
     *
     * @ORM\Column(name="c_settings", type="json")
     */
    private $settings=[];

    /**
     * @var \App\Entity\File
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\File", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="t_file_id", referencedColumnName="id")
     * })
     */
    private $file;

    public function __construct() {
        $this->file=File::createEmptyFile();
        $this->file->setPermissions(07, 06, 00);
        
        parent::__construct();
    }

    public function setOwner(?User $owner): Properties {
        
        $this->file->setOwner($owner);
        return parent::setOwner($owner);
    }
    
    public function setGroup(?Group $group): Properties {
        
        $this->file->setGroup($group);
        return parent::setGroup($group);
    }
    
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        
        return $this;
    }

    public function getSettings(): ?array
    {
        return $this->settings;
    }

    public function setSettings(array $settings): self
    {
        $this->settings = $settings;

        return $this;
    }
    
    public function getSetting(string $key,$default=null)
    {
        return isset($this->settings[$key])?$this->settings[$key]:$default;
    }
    
    public function setSetting(string $key, $value): self
    {
        $this->settings[$key] = $value;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }
}
