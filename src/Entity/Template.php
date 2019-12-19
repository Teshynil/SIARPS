<?php

namespace App\Entity;

use App\Entity\File;

class Template extends Properties 
{
    

    private $name;

    private $type;

    private $settings = [];

    private $file;

    

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
