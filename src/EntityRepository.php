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
     * Return an array of DeletionTargetInterface objects that reference the entity with the given ID.
     * The DeletionTargetInterface objects should be created with the referenced entity's repository and the referenced entity's ID.
     * If there are no referenced entities, return an empty array.
     *
     * @param int $entityId The ID of the entity.
     * @return DeletionTargetInterface[] An array of IDs of the referenced entities.
     */
    abstract public function getReferencedDeletionTargets(int $entityId): array;

    /**
     * Set the isDeletable property of the DeletionTargetInterface object to true or false, depending on whether the entity with the given ID is deletable.
     * Set the message property of the DeletionTargetInterface object to a message explaining why the entity is not deletable.
     * Return the DeletionTargetInterface object.
     *
     * example for a deletable entity:
     * $target->setIsDeletable(true);
     *
     * example for an undeletable entity with the default repository message:
     * $target->disableDeletion($unDeletableTargets);
     *
     * or with a custom message:
     *
     * $target->setMessage("THIS ENTITY CAN'T BE DELETED BECAUSE...");
     * $target->setIsDeletable(false);
     *
     * @param DeletionTargetInterface $target
     * @param DeletionTargetInterface[] $targets An array of DeletionTargetInterface objects that are being deleted.
     * @return bool
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
    public function getUnDeletableTargets(int $entityId, array $deletionTargets): array
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
                return $this->addTargetToUndeletables($referencedDeletionTarget, $unDeletableTargets);
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
