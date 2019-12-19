<?php
namespace App\Form\Requests;

use App\Entity\Group;
use App\Entity\User;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class CreatePropertiesRequest {
    
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
    
    public function fillProperties(\App\Entity\Properties &$object) {
        $object->setPermissions($this->ownerPermissions, $this->groupPermissions, $this->otherPermissions)
                ->setOwner($this->owner)
                ->setGroup($this->group);
    }
    
}