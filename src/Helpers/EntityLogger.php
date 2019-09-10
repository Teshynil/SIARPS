<?php

namespace App\Helpers;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\OnFlushEventArgs;

class EntityLogger {

    /**
     * @inheritDoc
     * @param OnFlushEventArgs $event
     * @return void
     */
    public function onFlush(OnFlushEventArgs $event) {
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();
        foreach ($uow->getScheduledEntityDeletions() as $deletion) {
            var_dump($deletion);
            die();
            if ($entity instanceof Post) {
                foreach ($deletion->getDeleteDiff() as $itemDeleted) {
                    if ($itemDeleted instanceof Comment) {
                        $historyRecord = new HistoryRecord($entity, $itemDeleted, 'remove');
                        $em->persist($historyRecord);
                        $uow->computeChangeSet($em->getClassMetadata(get_class($historyRecord)), $historyRecord);
                    }
                }
                foreach ($deletion->getInsertDiff() as $itemInserted) {
                    if ($itemInserted instanceof Comment) {
                        $historyRecord = new HistoryRecord($entity, $itemInserted, 'inserted');
                        $em->persist($historyRecord);
                        $uow->computeChangeSet($em->getClassMetadata(get_class($historyRecord)), $historyRecord);
                    }
                }
            }
        }
    }

}
