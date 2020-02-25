<?php

namespace App\Form\Requests;

use App\Entity\Project;
use App\Security\PermissionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditProjectRequest extends EditPropertiesRequest {

    /**
     * @NotBlank()
     * @var string 
     */
    public $name;

    /**
     * @NotBlank()
     * @var string 
     */
    public $description;

    /**
     *
     * @var array
     */
    public $documents = [];

    /**
     * @var Document
     *
     */
    public $summary;
    
    /**
     * @var Document
     *
     */
    public $progressDocument;

    /**
     *
     * @var Project 
     */
    private $entity;
    private $em;
    private $ps;

    public function __construct(EntityManagerInterface $em, PermissionService $ps) {
        $this->em = $em;
        $this->ps = $ps;
    }

    public function fillEntity(Project $project): self {
        $this->entity = $project;
        $this->owner = $project->getOwner();
        $this->group = $project->getGroup();
        $this->ownerPermissions = $project->getOwnerPermissions();
        $this->groupPermissions = $project->getGroupPermissions();
        $this->groupsPermissions = $project->getGroupsPermissions();
        $this->otherPermissions = $project->getOtherPermissions();
        $this->summary = $project->getSummary();
        $this->progressDocument = $project->getProgressDocument();

        $this->name = $project->getName();
        $this->description = $project->getDescription();
        $documents = $project->getDocuments();
        foreach ($documents as $document) {
            $documentRequest = new EditDocumentRequest();
            $documentRequest->fillEntity($document);
            $this->documents[] = $documentRequest;
        }

        return $this;
    }

    public function createEntity() {
        $project = $this->entity;
        $project->setName($this->name)
                ->setDescription($this->description)
                ->setSummary($this->summary)
                ->setProgressDocument($this->progressDocument);
        parent::fillProperties($project);

        $formDocuments = [];
        $newDocuments = [];
        foreach ($this->documents as $formDocument) {
            $formDocuments[] = $formDocument->createEntity();
            $newDocuments[] = $formDocument->createEntity();
        }
        foreach ($project->getDocuments() as $document) {
            $exists = false;
            foreach ($formDocuments as $key=>$formDocument) {
                if ($formDocument->getId() == $document->getId()) {
                    $exists = true;
                    unset($newDocuments[$key]);
                    break;
                }
            }
            if (!$exists) {
                $project->removeDocument($document);
            }
        }
        foreach ($newDocuments as $newDocument) {
            $project->addDocument($newDocument);
        }

        return $project;
    }

}
