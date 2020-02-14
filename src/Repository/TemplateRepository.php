<?php

namespace App\Repository;

use App\Entity\Template;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Template|null find($id, $lockMode = null, $lockVersion = null)
 * @method Template|null findOneBy(array $criteria, array $orderBy = null)
 * @method Template[]    findAll()
 * @method Template[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TemplateRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Template::class);
    }

    public function getAvailableTemplates(User $user) {
        $qb = $this->getEntityManager()->createQueryBuilder();
        if ($user->getAdminMode()) {
            $qb->select('t')
                    ->from(Template::class, 't')
            ;
        } else {
            $qb->select('t')
                    ->from(Template::class, 't')
                    ->join(User::class, 'u')
                    //->join('p.documents', 'd')
                    //->addSelect(['lastUpdate',])
                    ->where($qb->expr()->eq('u.id', $qb->expr()->literal($user->getId())))
                    ->andWhere(
                            $qb->expr()->orX(
                                    $qb->expr()->andX(
                                            $qb->expr()->eq('t.owner', 'u.id'),
                                            $qb->expr()->gt('BIT_AND(t.ownerPermissions, 4)', 0)
                                    ),
                                    $qb->expr()->andX(
                                            $qb->expr()->eq('t.group', 'u.group'),
                                            $qb->expr()->gt('BIT_AND(t.groupPermissions, 4)', 0)
                                    ),
                                    $qb->expr()->gt('BIT_AND(t.otherPermissions, 4)', 0)
                            )
                    )
            ;
        }
        $query = $qb->getQuery();
        return $query->getResult();
    }

    // /**
    //  * @return Template[] Returns an array of Template objects
    //  */
    /*
      public function findByExampleField($value)
      {
      return $this->createQueryBuilder('t')
      ->andWhere('t.exampleField = :val')
      ->setParameter('val', $value)
      ->orderBy('t.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    /*
      public function findOneBySomeField($value): ?Template
      {
      return $this->createQueryBuilder('t')
      ->andWhere('t.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */
}
