<?php

namespace Tests;

use IKadar\CascadingDeletion\DeletionTarget;
use IKadar\CascadingDeletion\DeletionTargetInterface;

class MockOrderRepository extends \IKadar\CascadingDeletion\EntityRepository
{

    /**
     * @inheritDoc
     */
    public function getReferencedDeletionTargets(int $entityId): array
    {
        if ($entityId == 1) {
            return [
                new DeletionTarget(11, new MockOrderItemRepository()),
                new DeletionTarget(12, new MockOrderItemRepository()),
                new DeletionTarget(13, new MockOrderItemRepository()),
                new DeletionTarget(14, new MockOrderShippingAddressRepository()),
                new DeletionTarget(15, new MockOrderBillingAddressRepository()),
            ];
        } else if ($entityId == 2) {
            return [
                new DeletionTarget(21, new MockOrderItemRepository()),
                new DeletionTarget(22, new MockOrderItemRepository()),
                new DeletionTarget(23, new MockOrderItemRepository()),
                new DeletionTarget(24, new MockOrderShippingAddressRepository()),
                new DeletionTarget(25, new MockOrderBillingAddressRepository()),
            ];
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public function checkDeletability(DeletionTargetInterface $target, array $targets): bool
    {
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
