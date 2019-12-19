<?php

namespace App\Helpers;

use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDirectory;
    
    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file): File
    {
        $dbfile=File::createFromUploadedFile($file);
        $file=$file->move($this->getTargetDirectory(), $dbfile->getId());
        $dbfile->setPath($file->getPathname());
        return $dbfile;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
