<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Project::class);
    }

//SELECT
//    *
//FROM
//    t_project p
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
    public function getAvailableProjects(User $user) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('p')
                ->from(Project::class, 'p')
                ->join(User::class, 'u')
                //->join('p.documents', 'd')
                //->addSelect(['lastUpdate',])
                ->where($qb->expr()->eq('u.id', $qb->expr()->literal($user->getId())))
                ->andWhere(
                        $qb->expr()->orX(
                                $qb->expr()->andX(
                                        $qb->expr()->eq('p.owner', 'u.id'),
                                        $qb->expr()->gt('BIT_AND(p.ownerPermissions, 4)', 0)
                                ),
                                $qb->expr()->andX(
                                        $qb->expr()->eq('p.group', 'u.group'),
                                        $qb->expr()->gt('BIT_AND(p.groupPermissions, 4)', 0)
                                ),
                                $qb->expr()->gt('BIT_AND(p.otherPermissions, 4)', 0)
                        )
                )
        ;
        $query = $qb->getQuery();
        return $query->getResult();
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
