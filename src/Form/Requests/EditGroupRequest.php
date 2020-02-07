<?php

namespace App\Form\Requests;

use App\Entity\Document;
use App\Entity\Group;
use App\Entity\Template;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class EditGroupRequest extends EditPropertiesRequest {

    /**
     * @NotBlank()
     * @Length(max=180)
     * @var string 
     */
    public $name;

    /**
     * @Choice(choices={true, false})
     * @var bool
     */
    public $fromActiveDirectory;

    /**
     * @Length(max=180)
     * @var string
     */
    public $dn;

    /**
     * 
     * @Length(max=180)
     * @var string 
     */
    public $description;

    /**
     * 
     * @var Group
     */
    public $entity;

    public function fillEntity(Group $group): self {
        $this->entity = $group;
        $this->owner = $group->getOwner();
        $this->group = $group->getGroup();
        $this->ownerPermissions = $group->getOwnerPermissions();
        $this->groupPermissions = $group->getGroupPermissions();
        $this->groupsPermissions = $group->getGroupsPermissions();
        $this->otherPermissions = $group->getOtherPermissions();

        $this->name = $group->getName();
        $this->description = $group->getDescription();
        $this->dn = $group->getDn();
        $this->fromActiveDirectory = !empty($this->dn);
        return $this;
    }

    public function createEntity() {
        $group = $this->entity;
        $group->setName($this->name)
                ->setDescription($this->description);

        if ($this->fromActiveDirectory) {
            $group->setDn($this->dn);
        }
        parent::fillProperties($group);

        return $group;
    }

}
