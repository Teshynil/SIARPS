<?php

namespace App\Repository;

use App\Entity\File;
use App\Entity\Version;
use App\Helpers\FormFieldResolver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @method Version|null find($id, $lockMode = null, $lockVersion = null)
 * @method Version|null findOneBy(array $criteria, array $orderBy = null)
 * @method Version[]    findAll()
 * @method Version[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VersionRepository extends ServiceEntityRepository {

    private $router;

    public function __construct(ManagerRegistry $registry, UrlGeneratorInterface $router) {
        $this->router = $router;
        parent::__construct($registry, Version::class);
    }

    public function getData(Version $document) {
        $data = $document->getData();
        if ($document->getLockState()) {
            $updated = false;
            if ($data['page'] == null) {
                $data['page'] = $document->getDocument()->getTemplate()->getSetting('page');
                $updated = true;
            }
            if ($data['template'] == null) {
                $data['template'] = $document->getDocument()->getTemplate()->getFile()->readFile("JSON");
                $updated = true;
            }
            if ($updated) {
                $document->fillFile($data);
            }
        }
        $resolved = [];
        foreach ($data['fields'] as $key => $field) {
            $resolved[$key] = $this->transformData($field,$document);
        }
        foreach ($resolved as $key => $value) {
            $data['fields'][$key] = $value;
        }
        return $data;
    }

public function transformData($data, Version $document) {
        $value = $data['value'];
        $type = $data['type'] ?? 'text';
        $output = $value;
        if (is_object($value)) {
            $class = get_class($value);
            if (!$this->getEntityManager()->getMetadataFactory()->isTransient($class)) {
                $entity = $this->getEntityManager()->find(get_class($value), $value->getId());

                if ($entity instanceof File) {
                    if (substr($entity->getMimeType(), 0, strlen("image/")) == "image/") {
                        $url = $this->router->generate("resource", ["method" => "view", "id" => $entity->getId()]);
                        $output = '<img src="' . $url . '"/ class="img-fluid" style="max-height: -webkit-fill-available;max-height: -moz-available;max-height: stretch;">';
                    } else {
                        $url = $this->router->generate("resource", ["method" => "download", "id" => $entity->getId()]);
                        $output = '<a href="' . $url . '"target="_blank" /><i class="fas fa-download fa-lg"></i></a>';
                    }
                } else {
                    $output = $entity;
                }
            }
        } else {
            $output = FormFieldResolver::resolveFieldToView($data, $this->getEntityManager(),$document);
        }
        return $output;
    }

    // /**
    //  * @return Version[] Returns an array of Version objects
    //  */
    /*
      public function findByExampleField($value)
      {
      return $this->createQueryBuilder('v')
      ->andWhere('v.exampleField = :val')
      ->setParameter('val', $value)
      ->orderBy('v.id', 'ASC')
      ->setMaxResults(10)
      ->getQuery()
      ->getResult()
      ;
      }
     */

    /*
      public function findOneBySomeField($value): ?Version
      {
      return $this->createQueryBuilder('v')
      ->andWhere('v.exampleField = :val')
      ->setParameter('val', $value)
      ->getQuery()
      ->getOneOrNullResult()
      ;
      }
     */
}
