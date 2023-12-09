<?php

namespace Tests;

use IKadar\CascadingDeletion\EntityRepository;
use IKadar\CascadingDeletion\DeletionTargetInterface;
use IKadar\CascadingDeletion\DeletionTarget;

class MockEntityARepository extends EntityRepository {

    /**
     * @inheritDoc
     */
    public function setDeletability(DeletionTargetInterface $target, array $targets): DeletionTargetInterface
    {
        $target->setIsDeletable(true);
        return $target;
    }

    public function getReferencedDeletionTargets(int $entityId): array
    {
        return [
            new DeletionTarget(10, new MockEntityB2Repository()),
            new DeletionTarget(11, new MockEntityB1Repository()),
            new DeletionTarget(12, new MockEntityB2Repository()),
        ];

    }

}
