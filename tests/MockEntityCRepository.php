<?php

namespace Tests;

use IKadar\CascadingDeletion\EntityRepository;
use IKadar\CascadingDeletion\DeletionTargetInterface;

class MockEntityCRepository extends EntityRepository {

    public function setDeletability(DeletionTargetInterface $target): DeletionTargetInterface
    {
        $target->setIsDeletable(false);
        $target->setMessage("THIS C CAN'T BE DELETED BECAUSE...");
//        $target->setIsDeletable(true);
        return $target;
    }

    public function getReferencedDeletionTargets(int $entityId): array
    {
        return [
        ];
    }
}
