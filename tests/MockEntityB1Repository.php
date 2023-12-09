<?php

namespace Tests;

use IKadar\CascadingDeletion\EntityRepository;
use IKadar\CascadingDeletion\DeletionTargetInterface;
use IKadar\CascadingDeletion\DeletionTarget;

class MockEntityB1Repository extends EntityRepository {

    public function setDeletability(DeletionTargetInterface $target): DeletionTargetInterface
    {
        $target->setIsDeletable(true);
        return $target;
    }

    public function getReferencedDeletionTargets(int $entityId): array
    {
        return [
            new DeletionTarget(111, new MockEntityCRepository()),
        ];

    }
}
