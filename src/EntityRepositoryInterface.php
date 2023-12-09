<?php /** @noinspection PhpUnused */

namespace IKadar\CascadingDeletion;

interface EntityRepositoryInterface {
    /**
     * Retrieves the IDs of entities referenced by the entity with the given ID.
     *
     * @param int $entityId The ID of the entity.
     * @return int[] An array of IDs of the referenced entities.
     */
    public function getReferencedDeletionTargets(int $entityId) :array;

    /**
     * Set the isDeletable property of the DeletionTargetInterface object to true or false, depending on whether the entity with the given ID is deletable.
     * Set the message property of the DeletionTargetInterface object to a message explaining why the entity is not deletable.
     * Return the isDeletable property of the $target DeletionTargetInterface object.
     *
     * example for a deletable entity:
     * $target->setIsDeletable(true);
     *
     * example for an undeletable entity with the default repository message:
     * $target->disableDeletion($unDeletableTargets);
     * or with a custom message:
     * $target->setMessage("THIS ENTITY CAN'T BE DELETED BECAUSE...");
     * $target->setIsDeletable(false);
     *
     * @param DeletionTargetInterface $target
     * @param DeletionTargetInterface[] $targets An array of DeletionTargetInterface objects that are being deleted.
     * @return bool
     */
    public function checkDeletability(DeletionTargetInterface $target, array $targets): bool;

    /**
     * Deletes the entity with the given ID.
     *
     * @param int $entityId The ID of the entity to delete.
     * @return void
     */
    public function performDeletion(int $entityId): void;

    /**
     * Checks if the entity with the given ID and its dependencies can be deleted.
     *
     * @param int $entityId The ID of the entity.
     * @param DeletionTargetInterface[] $deletionTargets
     * @return DeletionTargetInterface[]
     */
    public function getUnDeletableTargets(int $entityId, array $deletionTargets) : array;

    public function getDeletionConstraintMessage(array $unDeletableTargets): string;

}
