<?php

namespace App\Form\Requests;

use App\Entity\File;
use App\Entity\Version;
use App\Helpers\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EditVersionRequest extends EditPropertiesRequest {

    public $fields;
    public $fileUploader;
    public $em;
    private $originalFields;
    private $fileFields;

    /**
     *
     * @var Version
     */
    private $entity;

    public function __construct(FileUploader $fileUploader, EntityManagerInterface $em) {
        $this->fileUploader = $fileUploader;
        $this->em = $em;
    }

    public function fillEntity(Version $version): self {
        $this->entity = $version;
        $this->owner = $version->getOwner();
        $this->group = $version->getGroup();
        $this->ownerPermissions = $version->getOwnerPermissions();
        $this->groupPermissions = $version->getGroupPermissions();
        $this->groupsPermissions = $version->getGroupsPermissions();
        $this->otherPermissions = $version->getOtherPermissions();

        $baseFields = $version->getData()['fields'];
        $finalBaseFields = [];

        foreach ($baseFields as $key => $value) {
            if (!in_array($value['type'], ['file', 'image'])) {
                $finalBaseFields[$key] = $value['value'];
            } else {
                $this->fileFields[] = $key;
            }
        }

        $this->fields = $finalBaseFields;
        $this->originalFields = $version->getFields();
        return $this;
    }

    public function createEntity(array $_fields = []) {
        $version = $this->entity;
        $version->setDate(date_create());

        parent::fillProperties($version);
        $fileFields = [];
        foreach ($this->fileFields as $fileFieldKey) {
            
        }
        foreach ($this->fields as $key => $field) {
            if ($field instanceof UploadedFile) {
                $dbfile=$this->em->find(File::class, $this->originalFields[$key]->getId());
                $file = $this->fileUploader->replace($dbfile, $field);
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
            $value = $this->fields[$_field['name']];
            $this->fields[$_field['name']] = [
                'type' => $_field['type'],
                'value' => $value ?? $this->originalFields[$_field['name']]
            ];
        }
        $this->em->flush();
        return $version;
    }

}
