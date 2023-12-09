<?php
namespace IKadar\CascadingDeletion;

use IKadar\CascadingDeletion\DeletionTargetInterface;

/**
 * CascadingDeletionService handles the deletion of entities within a system,
 * ensuring that both the primary entity and its dependent entities are eligible for deletion.
 * This service maintains data integrity by performing a comprehensive check before deletion
 * and aborting the process if any entity in the deletion chain cannot be deleted.
 *
 * Key functionalities include:
 * - Collecting all entities to be deleted, including dependent entities.
 * - Performing pre-deletion checks to ensure all entities are eligible for deletion.
 * - Executing the deletion process for all collected entities.
 */
class CascadingDeletionService
{
    /**
     * Initiates the deletion process for a given entity.
     * This method orchestrates the process by collecting all dependent entities,
     * checking their eligibility for deletion, and then, if eligible, performing the deletion.
     *
     * @param DeletionTargetInterface $topTarget The top-level entity target for deletion.
     */    public function deleteEntity(DeletionTargetInterface $topTarget): void
    {
        // Collects the IDs of all entities to be deleted
        $deletionTargets = $this->collectDeletionTargets($topTarget);

        // Perform pre-deletion check all collected targets
        if ($this->checkDeletionEligibility($deletionTargets, $topTarget)) {

            // Perform deletion on all collected targets
            $this->performDeletion($deletionTargets);
            echo "All entities deleted successfully.\n";
        }
    }

    /**
     * Checks if all collected entities are eligible for deletion.
     * If any entity is found to be undeletable, the process is aborted and a message is output.
     *
     * @param array $deletionTargets Array of entities to be deleted.
     * @param DeletionTargetInterface $topTarget The top-level entity target for deletion.
     * @return bool Returns true if all entities are deletable, false otherwise.
     */
    private function checkDeletionEligibility(array $deletionTargets, DeletionTargetInterface $topTarget): bool
    {
        foreach ($deletionTargets as $deletionTarget) {
            $unDeletableTargets = $deletionTarget->getUnDeletableTargets($deletionTargets);

            if ($unDeletableTargets !== []) {
                $topTarget->setIsDeletable(false);
                $topTarget->setMessage("Top level message");
                $unDeletableTargets[] = $topTarget;
                echo sprintf(
                    "Deletion of %d from repository %s is not allowed.\n",
                    $deletionTarget->getEntityId(),
                    get_class($deletionTarget->getRepository())
                );

                var_dump($unDeletableTargets);
                return false;
            }
        }
        return true;
    }

    /**
     * Performs the deletion of all eligible entities.
     * This method is called after verifying that all entities in the deletion list are deletable.
     *
     * @param array $deletionTargets Array of entities to be deleted.
     */
    private function performDeletion(array $deletionTargets): void
    {
        foreach ($deletionTargets as $deletionTarget) {
            $deletionTarget->getRepository()->performDelete($deletionTarget->getEntityId());
        }
    }

    /**
     * Collects all entities that are to be deleted along with the given entity.
     * This method uses a recursive approach to identify and collect all dependent
     * entities that should be deleted.
     *
     * @param DeletionTargetInterface $target The entity from which to start collecting deletion targets.
     * @return DeletionTarget[] An array of DeletionTarget objects representing entities to be deleted.
     */
    private function collectDeletionTargets(DeletionTargetInterface $target): array
    {
        $targets = [];
        $this->collect($target, $targets);
        return $targets;
    }

    /**
     * A helper method to recursively collect deletion targets.
     * It ensures that each entity is only added once to the list of targets
     * and recursively collects targets from entities referenced by the current target.
     *
     * @param DeletionTargetInterface $target The current entity being processed.
     * @param DeletionTargetInterface[] $targets Reference to an array of already collected targets.
     */
    private function collect(DeletionTargetInterface $target, array &$targets): void
    {
        // Avoid re-adding the same entity
        if (isset($targets[$target->getEntityId()])) {
            return;
        }

        // Add the current entity to the targets
        $targets[$target->getEntityId()] = $target;

        // Get IDs of referenced entities and recursively collect their deletion targets
        $referencedDeletionTargets = $target->getRepository()->getReferencedDeletionTargets($target->getEntityId());

        foreach ($referencedDeletionTargets as $referencedDeletionTarget) {
            $this->collect(
                $referencedDeletionTarget,
                $targets
            );
        }
    }

}
