<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Version
 *
 * @ORM\Entity(repositoryClass="App\Repository\VersionRepository")
 */
class Version extends Properties {

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="c_date", type="datetime")
     */
    private $date;

    /**
     * @var \App\Entity\File
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\File",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="t_file_id", referencedColumnName="id")
     * })
     */
    private $file;

    /**
     * @var \App\Entity\Document
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Document", inversedBy="versions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="t_document_id", referencedColumnName="id")
     * })
     */
    private $document;
    private $data = null;

    public function __construct() {
        $this->file = File::createEmptyFile();
        $this->file->setPermissions(07, 06, 00);
        parent::__construct();
    }

    public function getDate(): ?\DateTimeInterface {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self {
        $this->date = $date;

        return $this;
    }

    public function getFile(): ?File {
        return $this->file;
    }

    public function setFile(?File $file): self {
        $this->file = $file;

        return $this;
    }

    public function fillFile(array $data): self {
        $file = $this->getFile()->getPath();
        $data = serialize($data);
        file_put_contents($file, $data);
        $this->getFile()->update();
        return $this;
    }

    public function getData(): ?array {
        if ($this->data == null) {
            $this->data = $this->getFile()->readFile('SERIALIZE');
        }

        return $this->data;
    }

    public function getFields(bool $withTypes = false): array {
        if ($this->data == null) {
            $this->getData();
        }
        if (isset($this->data['fields'])) {
            if ($withTypes) {
                $fields = $this->data['fields'];
            } else {
                $fields = array_combine(array_keys($this->data['fields']),array_column($this->data['fields'], 'value'));
            }
            return $fields;
        } else {
            return null;
        }
    }

    public function getField(string $name) {
        if ($this->data == null) {
            $this->getData();
        }
        if (isset($this->data['fields'][$name])) {
            return $this->data['fields'][$name]['value'];
        } else {
            return null;
        }
    }

    public function getDocument(): ?Document {
        return $this->document;
    }

    public function setDocument(?Document $document): self {
        $this->document = $document;

        return $this;
    }

}
