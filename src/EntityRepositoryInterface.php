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
     * Determines if the entity with the given ID is eligible for deletion.
     *
     * @param DeletionTargetInterface $target
     * @param DeletionTargetInterface[] $targets An array of DeletionTargetInterface objects that are being deleted.
     * @return DeletionTargetInterface
     */
    public function setDeletability(DeletionTargetInterface $target, array $targets): DeletionTargetInterface;

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
