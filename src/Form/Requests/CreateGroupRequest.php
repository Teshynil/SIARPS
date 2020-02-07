<?php

namespace App\Form\Requests;

use App\Entity\File as DBFile;
use App\Entity\Group;
use App\Entity\User;
use App\Helpers\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class CreateGroupRequest extends CreatePropertiesRequest {

    /**
     * @Blank()
     * @var Group
     */
    public $group;
    
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

    
    public function createEntity() {
        $group = new Group();
        $group->setName($this->name)
                ->setDescription($this->description);
        
        if($this->fromActiveDirectory){
            $group->setDn($this->dn);
        }
        parent::fillProperties($group);
        $group->setGroup($group);
        return $group;
    }

}
