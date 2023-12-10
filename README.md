# Cascading Deletion Service

## Overview
The Cascading Deletion Service is designed to manage and execute the deletion of entities within a system, focusing on the interdependencies between entities. The system is structured to ensure data integrity by comprehensively checking the eligibility for deletion of both primary and dependent entities, and by managing the cascading effect of deletions across related entities.

## Core Components

### 1. CascadingDeletionService (`CascadingDeletionService.php`)
**Purpose**: Manages the initiation and orchestration of the cascading deletion process. Provides a simple interface for deleting entities within the system. 

#### Key Functionalities:
- **Entity Deletion Handling**: Manages the deletion of entities, ensuring eligibility for both primary and dependent entities.
- **Data Integrity**: Performs comprehensive checks prior to deletion and aborts the process if any entity in the deletion chain cannot be deleted.
- **Deletion Process Execution**: Collects all entities to be deleted, verifies their eligibility, and executes the deletion process if all are eligible.

### 2. Entity Repository (`EntityRepository.php` and `EntityRepositoryInterface.php`)
**EntityRepository**: An abstract class implementing `EntityRepositoryInterface`. Provides the core logic for managing the deletion of entities. Subclasses implement the abstract methods to handle the specific relationships between entities. 
**EntityRepositoryInterface**: Defines the contract for repository operations. Outlines essential methods for managing deletion targets.

#### Key Functionalities:
- **Reference Entity Retrieval**: Identifies and retrieves entities referenced by a given entity, managing entity relationships and dependencies. Includes abstract methods `getReferencedDeletionTargets` and `getReferencedDeletionTargets`. These methods are implemented in subclasses to handle the specific relationships between entities.
- **Deletability Checks**: Determines if a given entity can be deleted, including abstract methods `getReferencedDeletionTargets` and `checkDeletability`.
- **Unalterable Core Logic**: The `final` designation of key methods ensures the consistency and integrity of the cascading deletion logic across the system.

#### Unalterable Core Logic:
- **Final Methods**: The following key methods are declared as `final` to preserve the central logic of the cascading deletion process across all subclasses and ensure the consistency and integrity of the cascading deletion logic across the system:
    - `final public function getUnDeletableTargets(int $entityId, array $deletionTargets): array`
    - `final public function addTargetToUndeletables(DeletionTargetInterface $target, array $undeletableTargets): void`


### 3. Deletion Targets (`DeletionTargetInterface.php` and `DeletionTarget.php`)
**DeletionTargetInterface**: Outlines essential methods for managing deletion targets.
**DeletionTarget**: Implements `DeletionTargetInterface`, representing entities within the system.

#### Properties and Methods:
- **Entity Identification**: Includes methods for getting entity ID and associated repository.
- **Deletion Status Management**: Methods for setting/getting `isDeletable` status and a message to explain deletion status.
- **State Tracking**: Incorporates a `checkingStarted` property to track the state of deletion eligibility checking, crucial for managing dependencies. Includes methods for setting/getting the checking state.

## System Functionality
The system is tailored to handle complex entity relationships, especially focusing on scenarios where deleting one entity triggers the deletion of related entities. The designation of certain methods as `final` in `EntityRepository` underscores the system's commitment to maintaining a consistent and robust deletion process. This cascading deletion process is meticulously designed to maintain data integrity, handle dependencies efficiently, and avoid issues like infinite loops in dependency checks.


# Usage Examples of CascadingDeletionService::deleteEntity Method

## Example of Implementing a Basic Repository Class

In the following example, we demonstrate how to create a simple repository class that extends the abstract `EntityRepository`. This class is responsible for managing a specific type of entity in the context of cascading deletions.

### BasicEntityRepository Example:

```php
class BasicEntityRepository extends EntityRepository {

    // Implementing the abstract method from EntityRepository
    public function getReferencedDeletionTargets(int $entityId): array {
        // Example logic: Retrieve IDs of entities referenced by the entity with the given ID
        // This is typically where you would query your database or data source
        // For simplicity, we're returning a static array of sample data
        return [
            new DeletionTarget(101, $this), // Assuming entity ID 101 is a dependent entity
            new DeletionTarget(102, $this)  // Assuming entity ID 102 is another dependent entity
        ];
    }

    // Implementing the abstract method from EntityRepository
    public function checkDeletability(DeletionTargetInterface $target, array $targets): bool {
        // Example logic: Check if the entity is deletable
        // This might involve checking certain conditions or rules
        // Returning true for simplicity
        return true;
    }

    // Implementing the abstract method from EntityRepository
    public function performDeletion(int $entityId): void {
        // Example logic: Perform the actual deletion of the entity
        // This is where you would typically delete the entity from your database or data source
        echo "Deleting entity with ID: " . $entityId . "\n";
    }
}
```


## Example: Deleting an Entity from the BasicEntityRepository
```php
$cascadingDeletionService = new CascadingDeletionService();
$basicEntityRepository = new BasicEntityRepository();
$entity = new DeletionTarget(200, $basicEntityRepository);
list($deletionSuccessful, $undeletableEntities) = $cascadingDeletionService->deleteEntity($entity);

if ($deletionSuccessful) {
    echo "Entity was deleted successfully.\n";
} else {
    echo "Deletion was aborted due to undeletable dependents.\n";
    echo "Undeletable entities: \n";
    foreach ($undeletableEntities as $undeletableEntity) {
        echo "Entity ID: " . $undeletableEntity->getEntityId() . "\n";
        echo "Repository: " . get_class($undeletableEntity->getRepository()) . "\n";
        echo "Message: " . $undeletableEntity->getMessage() . "\n";
    }
}
```
