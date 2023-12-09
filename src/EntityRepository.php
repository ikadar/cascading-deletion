<?php

namespace IKadar\CascadingDeletion;

abstract class EntityRepository implements EntityRepositoryInterface
{

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * Retrieves the IDs of entities referenced by the entity with the given ID.
     *
     * @param int $entityId The ID of the entity.
     * @return DeletionTargetInterface[] An array of IDs of the referenced entities.
     */
    abstract public function getReferencedDeletionTargets(int $entityId): array;

    /**
     * Determines if the entity with the given ID is eligible for deletion.
     *
     * @param DeletionTargetInterface $target
     * @return DeletionTargetInterface
     */
    abstract public function setDeletability(DeletionTargetInterface $target): DeletionTargetInterface;

    /**
     * Deletes the entity with the given ID.
     *
     * @param int $entityId The ID of the entity to delete.
     * @return void
     */
    public function performDelete(int $entityId): void
    {
        // Placeholder logic - override in subclass with actual implementation
        // Example: delete the entity from the database
        echo(sprintf(
            "%s deletes entity with ID %d\n",
            get_class($this),
            $entityId
        ));
    }

    /**
     * Returns an array of DeletionTarget objects, created from the undeletable entities.
     *
     * @param int $entityId The ID of the entity.
     * @return DeletionTargetInterface[] Undeletable deletion targets.
     */
    public function getUnDeletableTargets(int $entityId, array $deletionTargets): array
    {
        $target = $this->setDeletability(new DeletionTarget($entityId, $this));
        if ($target->getIsDeletable() === false) {
            return [$target];
        }

        $referencedDeletionTargets = $this->getReferencedDeletionTargets($entityId);
        foreach ($referencedDeletionTargets as $referencedDeletionTarget) {

            $unDeletableTargets = $referencedDeletionTarget->getUnDeletableTargets($deletionTargets);

            if ($unDeletableTargets !== []) {
                return $this->addTargetToUndeletables($referencedDeletionTarget, $unDeletableTargets);
            }
        }

        return [];
    }

    /**
     * @param DeletionTargetInterface[] $unDeletableTargets
     * @return string
     */
    public function getDeletionConstraintMessage($unDeletableTargets): string
    {
        return sprintf("Middle level message from %s", get_class($this));
    }

    /**
     * @param DeletionTargetInterface $target
     * @param DeletionTargetInterface[] $unDeletableTargets
     * @return DeletionTargetInterface[]
     */
    protected function addTargetToUndeletables(DeletionTargetInterface $target, array $unDeletableTargets): array
    {
        $topUnDeletableTarget = $unDeletableTargets[array_key_last($unDeletableTargets)];
        if ($target->getEntityId() !== $topUnDeletableTarget->getEntityId()) {
            $unDeletableTargets[] = $target->disableDeletion($unDeletableTargets);
        }
        return $unDeletableTargets;
    }

}
