<?php

namespace Tests;

use IKadar\CascadingDeletion\DeletionTarget;
use IKadar\CascadingDeletion\DeletionTargetInterface;

class MockOrderItemRepository extends \IKadar\CascadingDeletion\EntityRepository
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
        $target->setIsDeletable(true);
        return $target->getIsDeletable();
    }
}
