<?php

namespace Tests;

use IKadar\CascadingDeletion\EntityRepository;
use IKadar\CascadingDeletion\DeletionTargetInterface;

class MockEntityCRepository extends EntityRepository {

    /**
     * @inheritDoc
     */
    public function checkDeletability(DeletionTargetInterface $target, array $targets): bool
    {
        $target->setIsDeletable(false);
        $target->setMessage("THIS C CAN'T BE DELETED BECAUSE...");
//        $target->setIsDeletable(true);
        return $target->getIsDeletable();
    }

    /**
     * Return an array of DeletionTargetInterface objects that reference the entity with the given ID.
     * The DeletionTargetInterface objects should be created with the referenced entity's repository and the referenced entity's ID.
     * If there are no referenced entities, return an empty array.
     *
     * @example
     * return [
     *    new DeletionTarget(10, new MockEntityB2Repository()),
     *    new DeletionTarget(11, new MockEntityB1Repository()),
     *    new DeletionTarget(12, new MockEntityB2Repository()),
     * ];
     *
     * @param int $entityId
     * @return DeletionTargetInterface[]
     */
    public function getReferencedDeletionTargets(int $entityId): array
    {
        // return an empty array if there are no referenced entities
        return array();
    }
}
