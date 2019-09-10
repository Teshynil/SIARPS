<?php

namespace App\Security;

use App\Entity\Properties;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;

class PermissionService {

    private $entityManager;
    private $security;

    public function __construct(Security $security, EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function hasAccess(Properties $object, User $user = null) {
        if ($user == null) {
            $user = $this->security->getUser();
        }
    }

}
