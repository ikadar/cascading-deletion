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

        $deletionService->deleteEntity(new DeletionTarget(1, $mockEntityARepository));

        $this->assertTrue(true); // Example assertion
    }
}
