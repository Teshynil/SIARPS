<?php

namespace App\Repository;

use App\Entity\Proyect;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Proyect|null find($id, $lockMode = null, $lockVersion = null)
 * @method Proyect|null findOneBy(array $criteria, array $orderBy = null)
 * @method Proyect[]    findAll()
 * @method Proyect[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProyectRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Proyect::class);
    }

//SELECT
//    *
//FROM
//    t_proyect p
//    JOIN t_user u
//WHERE
//u.id = 'e506db8b-0aa4-11ea-a57f-e840f2eb9399'
//AND
//	(
//        (p.t_owner_id = u.id AND p.c_owner_permissions&4>0)
//    OR
//        (p.t_group_id = u.t_group_id AND p.c_other_permissions&4>0)
//    OR
//        (p.c_other_permissions&4>0)
//     )
    public function getDisponibleProyects(\App\Entity\User $user) {
        $qb = $this->getEntityManager()
                ->createQueryBuilder()
        ;
        $qb=$qb->select('p')
                ->from(\App\Entity\User::class, 'u')
                ->join(Proyect::class, 'p')
                ->where($qb->expr()->eq('u.id', $user->getId()))
                ->andWhere(
                        $qb->expr()->orX(
                                $qb->expr()->andX(
                                        $qb->expr()->eq('p.owner', 'u.id'),
                                        $qb->expr()->gt('p.ownerPermissions&4', 0)
                                ),
                                $qb->expr()->andX(
                                        $qb->expr()->eq('p.group', 'u.group'),
                                        $qb->expr()->gt('p.groupPermissions&4', 0)
                                ),
                                $qb->expr()->gt('p.otherPermissions&4', 0)
                        )
        );
        $qb=$qb->getQuery();
        return $qb->getResult();
    }

    /*
      public function findOneBySomeField($value): ?Proyect
      {
      return $this->createQueryBuilder('p')
      ->andWhere('p.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */
}
