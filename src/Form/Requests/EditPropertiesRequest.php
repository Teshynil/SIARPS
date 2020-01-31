<?php
namespace App\Form\Requests;

use App\Entity\Group;
use App\Entity\User;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Choice;

class EditPropertiesRequest {
    
    /**
     * 
     * @var User
     */
    public $owner;
    /**
     * 
     * @var Group
     */
    public $group;
    /**
     * 
     * @Range(min=0, max=7)
     * @var integer
     */
    public $ownerPermissions;
    /**
     * 
     * @Range(min=0, max=7)
     * @var integer
     */
    public $otherPermissions;
    /**
     * 
     * @Range(min=0, max=7)
     * @var integer
     */
    public $groupPermissions;
    
    /**
     * 
     * 
     * @var mixed
     */
    public $groupsPermissions;
    
    /**
     * @Choice(choices={true, false})
     * @var boolean 
     */
    public $locked;
    
    public function fillProperties(\App\Entity\Properties &$object) {
        $object->setPermissions($this->ownerPermissions, $this->groupPermissions, $this->otherPermissions)
                ->setOwner($this->owner)
                ->setGroup($this->group);
    }
}