<?php
namespace App\Form\Requests;

use App\Entity\Project;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateProjectRequest extends CreatePropertiesRequest {
    
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

    public function createEntity() {
        $project = new Project();
        $project->setName($this->name)
                ->setDescription($this->description);
        parent::fillProperties($project);
        
        foreach ($this->documents as $document) {
            $project->addDocument($document->createEntity());
        }
        
        return $project;
    }
    
}