<?php

namespace App\Repository;

use App\Entity\Setting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Setting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Setting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Setting[]    findAll()
 * @method Setting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SettingRepository extends ServiceEntityRepository {

    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, Setting::class);
    }

    public function getValue($key) {
        $setting = $this->find($key);
        $value = null;
        if ($setting !== null) {
            if ($setting->getEntity()) {
                if ($setting->getValue() !== null) {
                    $value = $this->getEntityManager()->getRepository($setting->getEntity())->find($setting->getValue());
                }else{
                    $value=null;
                }
            } else {
                $value = $setting->getValue();
            }
        }
        return $value;
    }

    public function setValue($key, $value) {
        $setting = $this->find($key);
        if ($setting !== null) {
            if ($setting->getEntity()) {
                $setting->setValue($value->getId());
            } else {
                $setting->setValue($value);
            }
        }
        $this->getEntityManager()->persist($setting);
    }

}
