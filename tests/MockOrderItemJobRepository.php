<?php

namespace Tests;

use IKadar\CascadingDeletion\DeletionTarget;
use IKadar\CascadingDeletion\DeletionTargetInterface;
use IKadar\CascadingDeletion\EntityRepository;

class MockOrderItemJobRepository extends EntityRepository
{

    /**
     * @inheritDoc
     */
    public function getReferencedDeletionTargets(int $entityId): array {
        $jobFileId = $entityId * 10 + 1;
        return [
            new DeletionTarget($jobFileId, new MockOrderItemJobFileRepository())
        ];
    }

    /**
     * @inheritDoc
     */
    public function checkDeletability(DeletionTargetInterface $target, array $targets): bool
    {
        return true; // Assuming no further dependencies
    }

    public function performDeletion(int $entityId): void
    {
        // Placeholder logic - override in subclass with actual implementation
        // Example: delete the entity from the database
        echo(sprintf(
            "%s deletes entity with ID %d\n",
            get_class($this),
            $entityId
        ));
    }

}
