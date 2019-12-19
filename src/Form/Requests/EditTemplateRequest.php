<?php
namespace App\Form\Requests;

use App\Entity\Group;
use App\Entity\Template;
use App\Entity\User;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class EditTemplateRequest extends EditPropertiesRequest {
    
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
    
    /**
     *
     * @var Template 
     */
    private $entity;

    public function fillEntity(Template $template): self {
        $this->entity=$template;
        $this->owner = $template->getOwner();
        $this->group = $template->getGroup();
        $this->ownerPermissions = $template->getOwnerPermissions();
        $this->groupPermissions = $template->getGroupPermissions();
        $this->groupsPermissions = $template->getGroupsPermissions();
        $this->otherPermissions = $template->getOtherPermissions();
        
        $this->name = $template->getName();
        $this->type = $template->getType();
        $this->templateForm = $template->getSetting("fields");
        return $this;
    }
    
    public function createEntity() {
        $template = $this->entity;
        $template->setName($this->name)
                ->setType($this->type);
        $template->setSetting('fields', $this->templateForm);
        return $template;
    }
    
}