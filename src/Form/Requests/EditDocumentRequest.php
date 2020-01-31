<?php

namespace App\Form\Requests;

use App\Entity\Document;
use App\Entity\Template;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditDocumentRequest extends EditPropertiesRequest {

    /**
     * @NotBlank()
     * @var string 
     */
    public $name;

    /**
     *
     * @var Template
     */
    public $template;
    public $entity;

    public function fillEntity(Document $document): self {
        $this->entity=$document;
        $this->owner = $document->getOwner();
        $this->group = $document->getGroup();
        $this->ownerPermissions = $document->getOwnerPermissions();
        $this->groupPermissions = $document->getGroupPermissions();
        $this->groupsPermissions = $document->getGroupsPermissions();
        $this->otherPermissions = $document->getOtherPermissions();

        $this->name = $document->getName();
        $this->template = $document->getTemplate();
        return $this;
    }

    public function createEntity() {
        $document = $this->entity;
        if ($document == null) {
            $document = new Document();
        }
        $document->setName($this->name)
                ->setTemplate($this->template);
        parent::fillProperties($document);

        return $document;
    }

}
