<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Symfony\Component\HttpFoundation\File\File as SysFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class File extends Properties {

    private $path;
    private $name;
    private $mimeType;
    private $size;
    private $creationDate;
    private $modificationDate;
    private $valid;
    private $file;

    public static function createFromUploadedFile(UploadedFile $uFile): File {
        $originalFilename = pathinfo($uFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $file = new File("", $safeName);
        $file->size = $uFile->getSize();
        $file->mimeType = $uFile->getMimeType();
        $file->creationDate = new \DateTime();
        $file->modificationDate = $file->creationDate;
        $file->file = null;
        return $file;
    }

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
        parent::__construct();
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
        if ($this->file == null || $this->path !== $this->file->getPathname()) {
            $this->file = new SysFile($this->path, false);
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

    public function getFile(): ?SysFile {
        return $this->file;
    }

    public function readFile(): string {
        $out="";
        if ($this->getValid()) {
            $f = $this->getFile()->openFile();
            $out=$f->fread($f->getSize());
            $f=null;
        }
        return $out;
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
