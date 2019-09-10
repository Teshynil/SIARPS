<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use JsonSerializable;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File as SysFile;

class File extends Properties {

    private $id;
    private $path;
    private $name;
    private $mimeType;
    private $size;
    private $creationDate;
    private $modificationDate;
    private $valid;
    private $file;

    public function __construct(string $path, string $name) {
        $this->path = $path;
        $this->name = $name;
        $this->mimeType = "text/plain";
        $this->size = 0;
        $this->file = new SysFile($path, false);
        $this->valid = $this->file->isFile();
        if ($this->valid) {
            $this->mimeType = $this->file->getMimeType();
            $this->size = $this->file->getSize();
        }
        $this->creationDate = new DateTime();
        $this->modificationDate = $this->creationDate;
    }

    public function prepareFile() {
        $this->file = new SysFile($this->path, false);
        if ($this->size !== $this->file->getSize() || $this->mimeType !== $this->file->getMimeType()) {
            $this->valid = false;
        }
    }

    public function getModificationDate() {
        return $this->modificationDate;
    }

    public function update() {
        if ($this->path !== $this->file->getPathname()) {
            $this->file = new SysFile($path, false);
            $this->valid = $this->file->isFile();
            $this->modificationDate = new DateTime();
            if ($this->valid) {
                $this->mimeType = $this->file->getMimeType();
                $this->size = $this->file->getSize();
            }
        } else {
            if ($this->size !== $this->file->getSize() || $this->mimeType !== $this->file->getMimeType()) {
                $this->valid = false;
                $this->modificationDate = new DateTime();
            }
        }
        return $this;
    }

    public function getId(): ?string {
        return $this->id;
    }

    public function getPath(): ?string {
        return $this->path;
    }

    public function setPath(string $path): self {
        $this->path = $path;
        return $this->update();
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;

        return $this->update();
    }

    public function getMimeType(): ?string {
        return $this->mimeType;
    }

    public function getSize(): ?int {
        return $this->size;
    }

    public function getCreationDate(): ?DateTimeInterface {
        return $this->creationDate;
    }

    public function isValid(): ?bool {
        return $this->valid;
    }

    public function setMimeType(string $mimeType): self {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function setSize(int $size): self {
        $this->size = $size;

        return $this;
    }

    public function getValid(): ?bool {
        return $this->valid;
    }

    public function setValid(bool $valid): self {
        $this->valid = $valid;

        return $this;
    }

    public function getFile() {
        return $this->file;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function setModificationDate(\DateTimeInterface $modificationDate): self {
        $this->modificationDate = $modificationDate;

        return $this;
    }

}
