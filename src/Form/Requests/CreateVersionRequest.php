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

    public function createEntity(array $_fields = []) {
        $version = new Version();
        $version->setDate(date_create());

        parent::fillProperties($version);
        $fileFields = [];
        foreach ($this->fields as $key => $field) {
            if ($field instanceof UploadedFile) {
                $file = $this->fileUploader->upload($field);
                $file->setOwner($this->owner)
                        ->setGroup($this->group)
                        ->setPermissions($this->ownerPermissions, $this->groupPermissions, $this->otherPermissions);
                $fileFields[$key] = $file;
            }
        }
        foreach ($fileFields as $key => $field) {
            $this->fields[$key] = $field;
            $this->em->persist($field);
        }
        foreach ($_fields as $_field) {
            if (isset($this->fields[$_field['name']])) {
                $value=$this->fields[$key];
                $this->fields[$key] = [
                    'type' => $_field['type'],
                    'value' => $value
                ];
            }
        }


        return $version;
    }

}
