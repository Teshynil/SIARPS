<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Properties
 *
 * @ORM\Table(name="t_properties")
 * @ORM\MappedSuperclass
 */
class Properties{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    protected $id;
    /**
     * @var \App\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="t_owner_id", referencedColumnName="id")
     * })
     */
    protected $owner;

    /**
     * @var \App\Entity\Group
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Group")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="t_group_id", referencedColumnName="id")
     * })
     */
    protected $group;
    /**
     * @var int
     *
     * @ORM\Column(name="c_owner_permissions", type="integer", nullable=false)
     */
    protected $ownerPermissions;

    /**
     * @var int
     *
     * @ORM\Column(name="c_other_permissions", type="integer", nullable=false)
     */
    protected $otherPermissions;

    /**
     * @var int
     *
     * @ORM\Column(name="c_group_permissions", type="integer", nullable=false)
     */
    protected $groupPermissions;

    /**
     * @var json
     *
     * @ORM\Column(name="c_groups_permissions", type="json", nullable=false)
     */
    protected $groupsPermissions;

    /**
     * @var bool
     *
     * @ORM\Column(name="c_lock_state", type="boolean", nullable=false)
     */
    protected $lockState;

    /**
     * @var \App\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="t_locked_by_id", referencedColumnName="id")
     * })
     */
    protected $lockedBy;

    public function __construct(bool $generateUUID = true) {
        if ($this->id == null) {
            $this->id = Uuid::uuid1();
        }
        $this->groupsPermissions = [];
        $this->lockState = false;
    }

    public function getId(): ?string {
        return $this->id;
    }

    public function getOwnerPermissions(): ?int {
        return $this->ownerPermissions;
    }

    public function setOwnerPermissions(int $ownerPermissions): self {
        $this->ownerPermissions = $ownerPermissions;

        return $this;
    }

    public function getOtherPermissions(): ?int {
        return $this->otherPermissions;
    }

    public function setOtherPermissions(int $otherPermissions): self {
        $this->otherPermissions = $otherPermissions;

        return $this;
    }

    public function getGroupsPermissions(): ?array {
        return $this->groupsPermissions;
    }

    public function setGroupsPermissions(array $groupsPermissions): self {
        $this->groupsPermissions = $groupsPermissions;

        return $this;
    }

    public function addGroupsPermissions(string $groupId, int $permission): self {
        $this->groupsPermissions[$groupId] = $permission;
        return $this;
    }

    public function removeGroupsPermissions(string $groupId): self {
        if (isset($this->groupsPermissions[$groupId])) {
            unset($this->groupsPermissions[$groupId]);
        }
        return $this;
    }

    public function getOwner(): ?User {
        return $this->owner;
    }

    public function setOwner(?User $owner): self {
        $this->owner = $owner;

        return $this;
    }

    public function getGroup() {
        return $this->group;
    }

    public function setGroup(?Group $group): self {
        $this->group = $group;

        return $this;
    }

    public function setPermissions(int $owner, int $group, int $other) {
        $this->setOwnerPermissions($owner);
        $this->setGroupPermissions($group);
        $this->setOtherPermissions($other);
        return $this;
    }

    public function getGroupPermissions(): ?int {
        return $this->groupPermissions;
    }

    public function setGroupPermissions(int $groupPermissions): self {
        $this->groupPermissions = $groupPermissions;

        return $this;
    }

    public function getLockState(): bool {
        return $this->lockState;
    }

    public function setLockState($lockState): self {
        $this->lockState = $lockState;
        return $this;
    }

    function getLockedBy(): ?User {
        return $this->lockedBy;
    }

    function setLockedBy($lockedBy): self {
        $this->lockedBy = $lockedBy;
        return $this;
    }

}
