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

    // Todo: rename it to getDependentTargets
    /**
     * @inheritDoc
     */
    abstract public function getReferencedDeletionTargets(int $entityId): array;

    /**
     * @inheritDoc
     */
    abstract public function checkDeletability(DeletionTargetInterface $target, array $targets): bool;

    /**
     * Deletes the entity with the given ID.
     *
     * @param int $entityId The ID of the entity to delete.
     * @return void
     */
    abstract public function performDeletion(int $entityId): void;

    /**
     * Returns an array of DeletionTarget objects, created from the undeletable entities.
     * The returned array should contain the given DeletionTarget object at the top, followed by the given undeletable entities.
     *
     * @param int $entityId The ID of the entity.
     * @param DeletionTargetInterface[] $deletionTargets Array of entities to be deleted.
     * @return DeletionTargetInterface[] Stack of undeletable entities in the deletion chain.
     */
    final public function getUnDeletableTargets(int $entityId, array $deletionTargets): array
    {
        // Check if the entity can be deleted
        $target = $deletionTargets[$entityId];

        // If the entity already has been checked, it must be deletable,
        // otherwise execution would not have reached this point, so return an empty array
        if ($target->isCheckingStarted() === true) {
            return [];
        }

        // Check if the entity can be deleted
        $isDeletable = $this->checkDeletability($target, $deletionTargets);
        // Mark the entity as checked
        $target->setCheckingStarted(true);
        if ($isDeletable === false) {
            $target->disableDeletion([$target]);
            return [$target];
        }

        // Make sure that isDeletable is null at this point,
        // so during checking the referenced entities, it won't give a false positive.
        // This way the dependencies can rely on the isDeletable property
        // of the parent entity in their checkDeletability method.
        $target->setIsDeletable(null);

        // Check if all referenced entities can be deleted
        $referencedDeletionTargets = $this->getReferencedDeletionTargets($entityId);
        foreach ($referencedDeletionTargets as $referencedDeletionTarget) {

            $unDeletableTargets = $referencedDeletionTarget->getUnDeletableDependencies($deletionTargets);

            if ($unDeletableTargets !== []) {
                $unDeletableTargets[] = $target->disableDeletion($unDeletableTargets);
            }
        }

        // Here we can set the isDeletable property of the entity to true,
        // because otherwise execution would not have reached this point
        $target->setIsDeletable(true);

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

}
