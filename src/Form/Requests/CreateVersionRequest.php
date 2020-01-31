<?php

namespace App\Form\Requests;

use App\Entity\Version;
use App\Helpers\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CreateVersionRequest extends CreatePropertiesRequest {

    public $fields;
    public $fileUploader;
    public $em;
    public function __construct(FileUploader $fileUploader, EntityManagerInterface $em) {
        $this->fileUploader = $fileUploader;
        $this->em = $em;
    }
    public function createEntity() {
        $version = new Version();
        $version->setDate(date_create());

        parent::fillProperties($version);
        $fileFields=[];
        foreach ($this->fields as $key=>$field) {
            if ($field instanceof UploadedFile) {
                $file = $this->fileUploader->upload($field);
                $file->setOwner($this->owner)
                        ->setGroup($this->group)
                        ->setPermissions($this->ownerPermissions, $this->groupPermissions, $this->otherPermissions);
                $fileFields[$key]=$file;
            }
        }
        foreach ($fileFields as $key=>$field) {
            $this->fields[$key]=$field;
            $this->em->persist($field);
        }
        

        return $version;
    }

}
