<?php

namespace Tests;

use IKadar\CascadingDeletion\DeletionTarget;
use IKadar\CascadingDeletion\DeletionTargetInterface;
use IKadar\CascadingDeletion\EntityRepository;

class MockOrderItemRepository extends EntityRepository
{

    /**
     * @inheritDoc
     */
    public function getReferencedDeletionTargets(int $entityId): array
    {
        $dependencies = [
            11 => [111, 112, 113],
            12 => [121, 122, 123],
            13 => [131, 132, 133],
            21 => [211, 212, 213],
            22 => [221, 222, 223],
            23 => [231, 232, 233],
        ];
        return array_map(function($id) {
            return new DeletionTarget($id, new MockOrderItemJobRepository());
        }, $dependencies[$entityId] ?? []);
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
