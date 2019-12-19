<?php
namespace App\Form\Requests;

use App\Entity\Group;
use App\Entity\User;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

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
        $template = new \App\Entity\Template();
        $template->setName($this->name)
                ->setType($this->type);
        parent::fillProperties($template);
        $template->setSetting('fields', $this->templateForm);
        return $template;
    }
    
}