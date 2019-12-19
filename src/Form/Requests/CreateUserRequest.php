<?php

namespace App\Form\Requests;

use App\Entity\File as DBFile;
use App\Entity\User;
use App\Helpers\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class CreateUserRequest extends CreatePropertiesRequest {

    /**
     * @Blank()
     * @var User
     */
    public $owner;

    /**
     * @Blank()
     * @var integer
     */
    public $ownerPermissions;

    /**
     * @Email(checkMX=false, checkHost=false)
     * @var string 
     */
    public $email;

    /**
     * @NotBlank()
     * @Length(min=3,max=64)
     * @var string 
     */
    public $password;

    /**
     * @NotBlank()
     * @Length(max=180)
     * @var string 
     */
    public $firstName;

    /**
     * @Length(max=180)
     * @var string 
     */
    public $lastName;

    /**
     * @NotBlank()
     * @Length(max=180)
     * @var string 
     */
    public $username;

    /**
     * @Image(minWidth=120, maxWidth=640, minHeight=120, maxHeight=640)
     * @var UploadedFile 
     */
    public $photo;
    private $fileUploader;
    private $passwordEncoder;

    public function __construct(FileUploader $fileUploader, UserPasswordEncoderInterface $passwordEncoder) {
        $this->fileUploader = $fileUploader;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function createEntity() {
        $user = new User();
        $user->setUsername($this->username)
                ->setEmail($this->email)
                ->setFirstName($this->firstName)
                ->setLastName($this->lastName);
        $this->owner=$user;
        $this->ownerPermissions=07;
        parent::fillProperties($user);
        $password = $this->passwordEncoder->encodePassword($user, $this->password);
        $user->setPassword($password);
        if ($this->photo instanceof UploadedFile) {
            $photo = $this->fileUploader->upload($this->photo);
            $photo->setOwner($user)
                    ->setGroup($user->getGroup())
                    ->setPermissions(07, 04, 04);
            $user->setPhoto($photo);
        }
        return $user;
    }

}
