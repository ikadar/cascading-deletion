<?php /** @noinspection PhpUnused */

namespace IKadar\CascadingDeletion;

interface EntityRepositoryInterface {

    /**
     * Retrieves the IDs of entities referenced by the entity with the given ID.
     * Return an array of DeletionTargetInterface objects that reference the entity with the given ID.
     * The DeletionTargetInterface objects should be created with the referenced entity's repository and the referenced entity's ID.
     * If there are no referenced entities, return an empty array.
     *
     * @param int $entityId The ID of the entity.
     * @return DeletionTargetInterface[] An array of IDs of the referenced entities.
     */
    public function getReferencedDeletionTargets(int $entityId) :array;

    /**
     * Checks if the entity with the given ID is deletable.
     * This method must not set the isDeletable property of the DeletionTargetInterface,
     * because it is called before all referenced entities have been checked.
     * If the entity is not deletable, it should set the message property of the DeletionTargetInterface.
     * Return true if the entity is deletable, false otherwise.
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

    public function getDeletionConstraintMessage(DeletionTargetInterface $target, array $unDeletableTargets): string;

}
