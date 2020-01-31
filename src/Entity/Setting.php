<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Properties;
/**
 * Setting
 *
 * @ORM\Entity(repositoryClass="App\Repository\SettingRepository")
 */
class Setting extends Properties {
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="c_entity", type="string", nullable=true)
     */
    private $entity;

    /**
     * @var \stdClass|null
     *
     * @ORM\Column(name="c_value", type="object", nullable=true)
     */
    private $value;

    public function __construct(string $id = null, string $entity = null, $value = null, $owner = null, $group = null, int $ownerPermissions = 07, int $groupPermissions = 06, int $otherPermissions = 0) {
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
        if ($value instanceof Properties) {
            $this->value = $value->getId();
            $this->entity = get_class($value);
        }else{
            $this->value = $value;
        }
        return $this;
    }

}
