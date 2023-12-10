<?php

namespace Tests;

use IKadar\CascadingDeletion\DeletionTarget;
use IKadar\CascadingDeletion\DeletionTargetInterface;

class MockOrderShippingAddressRepository extends \IKadar\CascadingDeletion\EntityRepository
{
    /**
     * @inheritDoc
     */
    public function getReferencedDeletionTargets(int $entityId): array {
        return [
            new DeletionTarget($entityId == 14 ? 1 : 2, new MockOrderRepository())
        ];
    }

    /**
     * @inheritDoc
     */
    public function checkDeletability(DeletionTargetInterface $target, array $targets): bool
    {
//        $target->disableDeletion([$target]);
//        return $target->getIsDeletable();
        $target->setIsDeletable(true);
        return $target->getIsDeletable();
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
