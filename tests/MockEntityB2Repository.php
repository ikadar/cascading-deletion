<?php

namespace Tests;

use IKadar\CascadingDeletion\EntityRepository;
use IKadar\CascadingDeletion\DeletionTargetInterface;

class MockEntityB2Repository extends EntityRepository {

    public function checkDeletability(DeletionTargetInterface $target, array $targets): bool
    {
        $target->setIsDeletable(true);
        return $target->getIsDeletable();
    }

    public function getReferencedDeletionTargets(int $entityId): array
    {
        return [
        ];

    }
}
