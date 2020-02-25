<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Helpers;

use App\Entity\File;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Description of FileSaver
 *
 * @author Teshynil
 */
class FileSaver {

    /**
     *
     * @var string 
     */
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

    public function prePersist(File $file, LifecycleEventArgs $event) {
        if ($file->getPath() == null) {
            $file->setPath(join(DIRECTORY_SEPARATOR, [$this->targetDirectory, $file->getId()]));
        }
        if ($file->getSize() == 0) {
            $this->fileSystem->dumpFile($file->getPath(), '');
        }
        $file->setCreationDate(new \DateTime());
    }

}
