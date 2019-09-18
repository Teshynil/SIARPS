<?php

namespace App\Security;

use App\Entity\Properties;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Security;

class PermissionService {

    const READ = 4;
    const WRITE = 2;
    const LOCK = 1;
    const DELETE = 3;

    private $entityManager;
    private $security;

    public function __construct(Security $security, EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function hasDelete(Properties $object, User $user = null): ?bool {
        return $this->hasAccess($object, $user, PermissionService::DELETE);
    }

    public function hasRead(Properties $object, User $user = null): ?bool {
        return $this->hasAccess($object, $user, PermissionService::READ);
    }

    public function hasWrite(Properties $object, User $user = null): ?bool {
        return $this->hasAccess($object, $user, PermissionService::WRITE);
    }

    public function hasLock(Properties $object, User $user = null): ?bool {
        return $this->hasAccess($object, $user, PermissionService::LOCK);
    }

    public function hasAccess(Properties $object, User $user = null, int $permission): ?bool {
        if ($user == null) {
            $user = $this->security->getUser();
        }
        $hasAccess = false;
        if ($user instanceof Properties) {
            $group = $user->getGroup();
            if ($user->getAdminMode() || $this->entityManager->getRepository(\App\Entity\Setting::class)->getValue("adminGroup") == $group) {
                return true;
            }
            if ($object->getOwner() == $user) {
                $hasAccess |= $this->checkPermission($object->getOwnerPermissions(), $permission);
            }
            if ($hasAccess)
                return true;
            if ($object->getGroup() == $group && $object->getGroup() !== null) {
                $hasAccess |= $this->checkPermission($object->getOwnGroupPermissions(), $permission);
            }
            if ($hasAccess)
                return true;
            $othersgroups = $object->getOthersGroupPermissions();
            $userGroupId = $group->getId();
            if (count($othersgroups) > 0) {
                foreach ($othersgroups as $id => $value) {
                    if ($userGroupId == $id) {
                        $hasAccess |= $this->checkPermission($value, $permission);
                        break;
                    }
                }
            }
            if ($hasAccess)
                return true;
            $hasAccess |= $this->checkPermission($object->getOtherPermissions(), $permission);
            return $hasAccess > 0;
        } else {
            throw new HttpException(500);
        }
        return false;
    }

    public function checkPermission(int $permissions, int $mask): ?bool {
        return ($permissions & $mask) > 0;
    }

}
