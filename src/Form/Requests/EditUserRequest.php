<?php

namespace App\Form\Requests;

use App\Entity\File as DBFile;
use App\Entity\User;
use App\Helpers\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class EditUserRequest extends EditPropertiesRequest {

    /**
     * @Email(checkMX=false, checkHost=false)
     * @var string 
     */
    public $email;

    /**
     * 
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

    /**
     * @Choice(choices={"local", "ldap"})
     * @var string 
     */
    public $loginMode;

    /**
     * 
     * @Length(max=500)
     * @var string 
     */
    public $dn;

    /**
     * @Callback
     * @param \App\Form\Requests\ExecutionContextInterface $context
     * @param type $payload
     */
    public function validate(ExecutionContextInterface $context, $payload) {
        if ($this->loginMode == 'ldap') {
            if (empty($this->dn) || $this->dn[0] == '#') {
                $context->buildViolation("Si el modo de autentificación esta asignado en Directorio Activo este campo no debe estar vacio o empezar con '#'")
                        ->atPath('dn')
                        ->addViolation();
            }
        }
    }

    private $fileUploader;
    private $passwordEncoder;

    /**
     *
     * @var User
     */
    private $entity;
    public function __construct(FileUploader $fileUploader, UserPasswordEncoderInterface $passwordEncoder) {
        $this->fileUploader = $fileUploader;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function fillEntity(User $user): self {
        $this->entity=$user;
        $this->owner = $user->getOwner();
        $this->group = $user->getGroup();
        $this->ownerPermissions = $user->getOwnerPermissions();
        $this->groupPermissions = $user->getGroupPermissions();
        $this->groupsPermissions = $user->getGroupsPermissions();
        $this->otherPermissions = $user->getOtherPermissions();
        $this->username = $user->getUsername();
        $this->firstName = $user->getFirstName();
        $this->lastName = $user->getLastName();
        $this->email = $user->getEmail();
        $this->loginMode = $user->isLdapLogin() ? 'ldap' : 'local';
        $this->dn = $user->getDn();
        return $this;
    }

    public function createEntity(): User {
        $user = $this->entity;
        $user->setUsername($this->username)
                ->setEmail($this->email)
                ->setFirstName($this->firstName)
                ->setLastName($this->lastName)
                ->setPermissions(07, $this->groupPermissions, $this->otherPermissions)
                ->setOwner($user)
                ->setGroup($this->group);
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
