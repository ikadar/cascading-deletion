<?php
namespace IKadar\CascadingDeletion;

use IKadar\CascadingDeletion\DeletionTargetInterface;

class CascadingDeletionService
{
    /**
     * Deletes the entity with the given ID using the provided repository.
     *
     * @param DeletionTargetInterface $topTarget
     */
    public function deleteEntity(DeletionTargetInterface $topTarget): void
    {
        // Collects the IDs of all entities to be deleted
        $deletionTargets = $this->collectDeletionTargets($topTarget);

        // Perform pre-deletion check all collected targets
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
                return;
            }
        }

        // Perform deletion on all collected targets
        foreach ($deletionTargets as $deletionTarget) {
            $deletionTarget->getRepository()->performDelete($deletionTarget->getEntityId());
        }

        echo "All entities deleted successfully.\n";
    }

    /**
     * Collects the IDs of all entities that should be deleted.
     *
     * @param DeletionTargetInterface $target
     * @return DeletionTarget[] An array of DeletionTarget objects representing entities to be deleted.
     */
    private function collectDeletionTargets(DeletionTargetInterface $target): array
    {
        $targets = [];
        $this->collect($target, $targets);
        return $targets;
    }

    /**
     * Helper method to recursively collect deletion targets.
     *
     * @param DeletionTargetInterface $target
     * @param DeletionTargetInterface[] $targets
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
