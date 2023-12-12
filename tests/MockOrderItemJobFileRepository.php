<?php

namespace Tests;

use IKadar\CascadingDeletion\DeletionTarget;
use IKadar\CascadingDeletion\DeletionTargetInterface;

class MockOrderItemJobFileRepository extends \IKadar\CascadingDeletion\EntityRepository
{

    /**
     * @inheritDoc
     */
    public function getReferencedDeletionTargets(int $entityId): array
    {
        return []; // Assuming no further dependencies
    }

    /**
     * @inheritDoc
     */
    public function checkDeletability(DeletionTargetInterface $target, array $targets): bool
    {
        return false; // Assuming no further dependencies
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
