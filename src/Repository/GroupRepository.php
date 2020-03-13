<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Group::class);
    }

    public function getAvailable($user) {
        $min = 0;
        if ($user->getAdminMode()) {
            $min = -1;
        }
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p')
                ->from(Group::class, 'p')
                ->join(User::class, 'u')
                //->join('p.documents', 'd')
                //->addSelect(['lastUpdate',])
                ->where($qb->expr()->eq('u.id', $qb->expr()->literal($user->getId())))
                ->andWhere(
                        $qb->expr()->orX(
                                $qb->expr()->andX(
                                        $qb->expr()->eq('p.owner', 'u.id'),
                                        $qb->expr()->gt('BIT_AND(p.ownerPermissions, 4)', $min)
                                ),
                                $qb->expr()->andX(
                                        $qb->expr()->eq('p.group', 'u.group'),
                                        $qb->expr()->gt('BIT_AND(p.groupPermissions, 4)', $min)
                                ),
                                $qb->expr()->gt('BIT_AND(p.otherPermissions, 4)', $min)
                        )
                )
        ;
        $query = $qb->getQuery();
        return $query->getResult();
    }

}
