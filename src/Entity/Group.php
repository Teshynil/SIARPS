<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Group
 *
 * @ORM\Entity(repositoryClass="App\Repository\GroupRepository")
 */
class Group extends Properties {

    /**
     * @var string
     *
     * @ORM\Column(name="c_name", type="string", length=180, nullable=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="c_dn", type="string", length=512, nullable=true)
     */
    private $dn;

    /**
     * @var string|null
     *
     * @ORM\Column(name="c_description", type="string", nullable=true)
     */
    private $description;
    

    

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

    public function getDn(): ?string
    {
        return $this->dn;
    }

    public function setDn(?string $dn): self
    {
        $this->dn = $dn;

        return $this;
    }

}
