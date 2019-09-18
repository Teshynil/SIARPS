<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserProviderInterface {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, User::class);
    }

    public function loadUserByUsername($username): UserInterface {

    }

    public function refreshUser(UserInterface $user): UserInterface {
        $refreshUser = $this->find($user->getId());
        $refreshUser->setAdminMode($user->getAdminMode());
        return $refreshUser;
    }

    public function supportsClass($class): bool {

    }

}
