<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User extends Properties implements UserInterface, EquatableInterface {

    private $id;
    private $dn;
    private $email;
    private $password;
    private $firstName;
    private $lastName;
    private $username;
    private $notifications;
    private $photo;
    private $adminMode = false;

    public function __construct() {
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?string {
        return $this->id;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(?string $email): self {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials() {

    }

    public function getRoles() {
        return ['ROLE_USER'];
    }

    public function getSalt() {
        return $this->getId();
    }

    public function getUsername(): string {
        return $this->username;
    }

    public function getDn() {
        return $this->dn;
    }

    public function setDn($dn) {
        $this->dn = $dn;
        return $this;
    }

    public function getFirstName(): ?string {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self {
        $this->lastName = $lastName;

        return $this;
    }

    public function setUsername(string $username): self {
        $this->username = $username;

        return $this;
    }

    public function getFullName(): ?string {
        return $this->getFirstName() . (empty($this->getLastName()) ? "" : " " . $this->getLastName());
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self {
        if ($this->notifications->contains($notification)) {
            $this->notifications->removeElement($notification);
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    public function __sleep() {
        return ['id', 'username', 'password', 'adminMode'];
    }

    public function isEqualTo(UserInterface $user) {
        if ($this->getPassword() !== $user->getPassword()) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        if ($this->getUsername() !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    public function getPhoto(): ?File {
        return $this->photo;
    }

    public function setPhoto(?File $photo): self {
        $this->photo = $photo;

        return $this;
    }

    public function getAdminMode() {
        return $this->adminMode;
    }

    public function setAdminMode($adminMode) {
        $this->adminMode = $adminMode;
        return $this;
    }

}
