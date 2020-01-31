<?php
namespace App\Form\Requests;

use App\Entity\Document;
use App\Entity\Template;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateDocumentRequest extends CreatePropertiesRequest {
    
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

    public function createEntity() {
        $document = new Document();
        $document->setName($this->name)
                ->setTemplate($this->template);
        parent::fillProperties($document);
        
        return $document;
    }
    
}