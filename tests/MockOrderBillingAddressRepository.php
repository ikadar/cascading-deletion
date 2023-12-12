<?php

namespace Tests;

use IKadar\CascadingDeletion\DeletionTarget;
use IKadar\CascadingDeletion\DeletionTargetInterface;
use IKadar\CascadingDeletion\EntityRepository;

class MockOrderBillingAddressRepository extends EntityRepository
{
    /**
     * @inheritDoc
     */
    public function getReferencedDeletionTargets(int $entityId): array {
        return [
            new DeletionTarget($entityId == 15 ? 1 : 2, new MockOrderRepository())
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
