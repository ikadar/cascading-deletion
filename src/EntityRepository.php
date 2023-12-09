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
        $target = new DeletionTarget($entityId, $this);
        if ($this->checkDeletability($target, $deletionTargets) === false) {
            return [$target];
        }

        $referencedDeletionTargets = $this->getReferencedDeletionTargets($entityId);
        foreach ($referencedDeletionTargets as $referencedDeletionTarget) {

            $unDeletableTargets = $referencedDeletionTarget->getUnDeletableDependencies($deletionTargets);

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
