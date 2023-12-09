<?php

namespace Tests;

use IKadar\CascadingDeletion\EntityRepository;
use IKadar\CascadingDeletion\DeletionTargetInterface;

class MockEntityB2Repository extends EntityRepository {

    public function setDeletability(DeletionTargetInterface $target): DeletionTargetInterface
    {
        $target->setIsDeletable(true);
        return $target;
    }

    public function getReferencedDeletionTargets(int $entityId): array
    {
        return [
        ];

    }
}
