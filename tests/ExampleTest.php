<?php
namespace Tests;

use IKadar\CascadingDeletion\CascadingDeletionService;
use PHPUnit\Framework\TestCase;
use IKadar\CascadingDeletion\DeletionTarget;


class ExampleTest extends TestCase {

    /*

Test dependency graph:

MockOrderRepository
    - ID 1 dependencies:
        - MockOrderItem 11, 12, 13
        - MockOrderShippingAddress 14
        - MockOrderBillingAddress 15
    - ID 2 dependencies:
        - MockOrderItem 21, 22, 23
        - MockOrderShippingAddress 24
        - MockOrderBillingAddress 25
MockOrderItemRepository
    ID 11 dependencies:
        - MockOrderItemJob 111, 112, 113
    ID 12 dependencies:
        - MockOrderItemJob 121, 122, 123
    ID 13 dependencies:
        - MockOrderItemJob 131, 132, 133
    ID 21 dependencies:
        - MockOrderItemJob 211, 212, 213
    ID 22 dependencies:
        - MockOrderItemJob 221, 222, 223
    ID 23 dependencies:
        - MockOrderItemJob 231, 232, 233
MockOrderItemJobRepository
    ID 111 dependencies:
        - MockOrderItemJobFile 1111
    ID 112 dependencies:
        - MockOrderItemJobFile 1121
    ID 113 dependencies:
        - MockOrderItemJobFile 1131
    ID 121 dependencies:
        - MockOrderItemJobFile 1211
    ID 122 dependencies:
        - MockOrderItemJobFile 1221
    ID 123 dependencies:
        - MockOrderItemJobFile 1231
    ID 131 dependencies:
        - MockOrderItemJobFile 1311
    ID 132 dependencies:
        - MockOrderItemJobFile 1321
    ID 133 dependencies:
        - MockOrderItemJobFile 1331
    ID 211 dependencies:
        - MockOrderItemJobFile 2111
    ID 212 dependencies:
        - MockOrderItemJobFile 2121
    ID 213 dependencies:
        - MockOrderItemJobFile 2131
    ID 221 dependencies:
        - MockOrderItemJobFile 2211
    ID 222 dependencies:
        - MockOrderItemJobFile 2221
    ID 223 dependencies:
        - MockOrderItemJobFile 2231
    ID 231 dependencies:
        - MockOrderItemJobFile 2311
    ID 232 dependencies:
        - MockOrderItemJobFile 2321
    ID 233 dependencies:
        - MockOrderItemJobFile 2331
MockOrderItemJobFileRepository
MockOrderShippingAddressRepository
    ID 14 dependencies:
        - MockOrder 1
    ID 24 dependencies:
        - MockOrder 2
MockOrderBillingAddressRepository
    ID 15 dependencies:
        - MockOrder 1
    ID 25 dependencies:
        - MockOrder 2
*/


    public function testSomething() {

        $mockOrderRepository = new MockOrderRepository();

        $result = $mockOrderRepository->getReferencedDeletionTargets(1);

//        $this->assertIsArray($result);
//        $this->assertEquals([
//                new DeletionTarget(10, new MockEntityB2Repository()),
//                new DeletionTarget(11, new MockEntityB1Repository()),
//                new DeletionTarget(12, new MockEntityB2Repository()),
//            ],
//            $result
//        );

        $deletionService = new CascadingDeletionService();

        $deletionTarget = new DeletionTarget(1, $mockOrderRepository);

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
