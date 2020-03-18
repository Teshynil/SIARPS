<?php

namespace App\Repository;

use App\Entity\Document;
use App\Entity\Group;
use App\Entity\Project;
use App\Entity\Setting;
use App\Entity\User;
use App\Entity\Version;
use DateTime;
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

    public function getAvailableGrouped($user) {
        $projects = $this->getAvailable($user);
        $grouped = [];
        foreach ($projects as $project) {
            if ($project instanceof Project) {
                $grouped[$project->getGroup()->getId()][] = $project;
            }
        }

        return $grouped;
    }

    public function getAvailable($user) {
        $min = 0;
        if ($user->getAdminMode()) {
            $min = -1;
        }
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

    public function getSummary($user, ?Group $group = null) {
        $min = 0;
        if ($user->getAdminMode()) {
            $min = -1;
        }
        $data = [];
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('count(p)')
                ->from(Project::class, 'p')
                ->join(User::class, 'u')
                
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
                ->andWhere(
                        $qb->expr()->eq('p.lockState', 1)
                )
        ;
        if ($group instanceof Group) {
            $qb->join('p.group', 'g');
            $qb->andWhere(
                    $qb->expr()->like('g.id', $qb->expr()->literal($group->getId()))
            );
        }
        $query = $qb->getQuery();
        $data['done'] = (int) $query->getSingleScalarResult();
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('count(p)')
                ->from(Project::class, 'p')
                ->join(User::class, 'u')
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
                ->andWhere(
                        $qb->expr()->eq('p.lockState', 0)
                )
        ;
        if ($group instanceof Group) {
            $qb->join('p.group', 'g');
            $qb->andWhere(
                    $qb->expr()->like('g.id', $qb->expr()->literal($group->getId()))
            );
        }
        $query = $qb->getQuery();
        $data['now'] = (int) $query->getSingleScalarResult();
        return $data;
    }

    public function loadSnapshot($id, DateTime $date): ?Project {
        $project = $this->find($id);
        $documents = $project->getDocuments();
        foreach ($documents as $document) {
            if ($document instanceof Document) {
                $versions = $document->getVersions();
                $removes = [];
                foreach ($versions as $version) {
                    if ($version instanceof Version)
                        if ($version->getDate() > $date) {
                            $removes[] = $version;
                        }
                }
                foreach ($removes as $remove) {
                    $document->removeVersion($remove);
                }
            }
        }
        return $project;
    }

    public function getGlobalProject(): ?Project {
        return $this->getEntityManager()->getRepository(Setting::class)->getValue('globalProject');
    }

}
