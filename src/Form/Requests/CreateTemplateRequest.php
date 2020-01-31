<?php
namespace App\Form\Requests;

use App\Entity\Template;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreateTemplateRequest extends CreatePropertiesRequest {
    
    /**
     * @NotBlank()
     * @var string 
     */
    public $name;

    /**
     * @Choice(choices={"File","Form"})
     * @var string
     */
    public $type;

    /**
     *
     * @var array 
     */
    public $templateForm = [];

    public function createEntity() {
        $template = new Template();
        $template->setName($this->name)
                ->setType($this->type);
        parent::fillProperties($template);
        $template->setSetting('fields', $this->templateForm);
        return $template;
    }
    
}