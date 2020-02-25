<?php

namespace App\Helpers;

use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader {

    private $targetDirectory;

    /**
     *
     * @var Filesystem
     */
    private $fileSystem;

    public function __construct($targetDirectory = null, Filesystem $fileSystem) {
        $this->targetDirectory = $targetDirectory;
        $this->fileSystem = $fileSystem;
    }

    public function replace(?File $dbfile, UploadedFile $file): File {
        if ($dbfile == null) {
            return $this->upload($file);
        } else {
            unlink($dbfile->getPath());
            $file = $file->move($this->getTargetDirectory(), $dbfile->getId());
            $dbfile->setPath($file->getPathname());
            $dbfile->update();
            return $dbfile;
        }
    }

    public function upload(UploadedFile $file): File {
        $dbfile = File::createFromUploadedFile($file);
        $file = $file->move($this->getTargetDirectory(), $dbfile->getId());
        $dbfile->setPath($file->getPathname());
        return $dbfile;
    }

    public function getTargetDirectory() {
        return $this->targetDirectory;
    }

}
