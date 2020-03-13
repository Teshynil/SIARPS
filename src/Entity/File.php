<?php

namespace App\Entity;

use App\Repository\FileRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File as SysFile;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * File
 * @ORM\Entity(repositoryClass="FileRepository")
 */
class File extends Properties {

    /**
     * @var string|null
     *
     * @ORM\Column(name="c_path", type="string", length=512, nullable=true, unique=true)
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="c_name", type="string", length=255, nullable=false, unique=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="c_mime_type", type="string", length=255, nullable=false, unique=false)
     */
    private $mimeType;

    /**
     * @var int
     *
     * @ORM\Column(name="c_size", type="integer", nullable=false)
     */
    private $size;

    /**
     * @var bool
     *
     * @ORM\Column(name="c_valid", type="boolean", nullable=false)
     */
    private $valid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="c_creation_date", type="datetime")
     */
    private $creationDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="c_modification_date", type="datetime")
     */
    private $modificationDate;
    private $file;

    public static function createEmptyFile(): File {
        $file = new File();
        $file->size = 0;
        $file->mimeType = "text/plain";
        $file->creationDate = new \DateTime();
        $file->modificationDate = $file->creationDate;
        $file->file = null;
        return $file;
    }

    public static function createFromUploadedFile(UploadedFile $uFile): File {
        $originalFilename = pathinfo($uFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $file = new File(null, $safeName);
        $file->size = $uFile->getSize();
        $file->mimeType = $uFile->getMimeType();
        $file->creationDate = new \DateTime();
        $file->modificationDate = $file->creationDate;
        $file->file = null;
        return $file;
    }

    public function __construct(string $path = null, string $name = null) {
        parent::__construct();
        $this->path = $path;
        if ($name == null) {
            $this->name = $this->id->toString();
        } else {
            $this->name = $name;
        }
        $this->mimeType = "text/plain";
        $this->size = 0;
        $this->file = new SysFile($path ?? "", false);
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
            $this->setValid(false);
        }
    }

    public function getModificationDate() {
        return $this->modificationDate;
    }

    /**
     * 
     * @ORM\PreFlush
     */
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
            $this->file = new SysFile($this->path, false);
            if ($this->size !== $this->file->getSize() || $this->mimeType !== $this->file->getMimeType()) {
                $this->mimeType = $this->file->getMimeType();
                $this->size = $this->file->getSize();
                $this->valid = true;
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
        $this->prepareFile();
        return $this->file;
    }

    public function readFile($type = "TEXT") {
        $this->prepareFile();
        $out = "";
        if ($this->getValid()|| true) {
            $f = $this->getFile()->openFile();
            if ($f->getSize() > 0) {
                $out = $f->fread($f->getSize());
            }
            $f = null;
        }else{
            throw new HttpException(403, "El recurso no es valido verificar el estado del archivo");
        }
        switch ($type) {
            case 'SERIALIZE':
                $out = unserialize($out);
                break;
            case 'JSON':
                $out = json_decode($out,TRUE);
                break;
            case 'TEXT':
            default:
                break;
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

    public function __sleep() {
        return ['id','path','name','mimeType','size','valid'];
    }
}
