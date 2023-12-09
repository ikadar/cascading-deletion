<?php

namespace Tests;

use IKadar\CascadingDeletion\EntityRepository;
use IKadar\CascadingDeletion\DeletionTargetInterface;
use IKadar\CascadingDeletion\DeletionTarget;

class MockEntityB1Repository extends EntityRepository {

    public function checkDeletability(DeletionTargetInterface $target, array $targets): bool
    {
        $target->setIsDeletable(true);
        return $target->getIsDeletable();
    }

    public function getReferencedDeletionTargets(int $entityId): array
    {
        return [
            new DeletionTarget(111, new MockEntityCRepository()),
        ];

    }
}
