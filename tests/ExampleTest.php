<?php
namespace Tests;

use IKadar\CascadingDeletion\CascadingDeletionService;
use PHPUnit\Framework\TestCase;
use IKadar\CascadingDeletion\DeletionTarget;


class ExampleTest extends TestCase {
    public function testSomething() {

        $mockEntityARepository = new MockEntityARepository();

        $result = $mockEntityARepository->getReferencedDeletionTargets(1);

        $this->assertIsArray($result);
        $this->assertEquals([
                new DeletionTarget(10, new MockEntityB2Repository()),
                new DeletionTarget(11, new MockEntityB1Repository()),
                new DeletionTarget(12, new MockEntityB2Repository()),
            ],
            $result
        );

        $deletionService = new CascadingDeletionService();

        $deletionTarget = new DeletionTarget(1, $mockEntityARepository);
        list($success, $undeletables) = $deletionService->deleteEntity($deletionTarget);

        if ($success) {
            echo "All entities deleted successfully.\n";
        } else if ($undeletables) {

            echo sprintf(
                "Deletion of %d from repository %s is not allowed.\n",
                $deletionTarget->getEntityId(),
                get_class($deletionTarget->getRepository())
            );

            var_dump($undeletables);

            foreach ($undeletables as $undeletable) {
                echo sprintf("%s\n", $undeletable->getMessage());
            }

        }

        $this->assertTrue(true); // Example assertion
    }
}
