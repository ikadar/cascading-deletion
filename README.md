
# Entity Deletion Service

## Purpose

The Entity Deletion Service is designed to manage the deletion of entities in a system where entities have dependent relationships with each other. This service ensures that an entity can only be deleted if it and all its dependencies are eligible for deletion, thus maintaining data integrity and preventing partial deletions.

## Implementation

### DeletableEntityInterface

`DeletableEntityInterface` is an interface that all entities must implement to work with the Entity Deletion Service. It declares four essential methods:

- `getReferencedEntities()`: Returns an array of entities that the current entity references.
- `canBeDeleted(&$targets, &$undeletableEntities)`: Recursively checks if the entity and its dependencies can be deleted. It also collects all entities in the deletion chain.
- `isSelfDeletable()`: Determines if the entity itself (ignoring its dependencies) is eligible for deletion.
- `delete()`: Contains the logic to delete the entity.

### Abstract Entity Class

The abstract `Entity` class implements `DeletableEntityInterface`. It provides the basic implementation of the interface methods. Specific entity classes extend this class and can override these methods to implement custom behavior.

### Specific Entity Classes

- `EntityA`, `EntityB`, `EntityC`, and `EntityD` are concrete classes extending the abstract `Entity` class. They represent different types of entities in the system.
- These classes primarily provide their own implementations of `getReferencedEntities()` and, if needed, `isSelfDeletable()`.

### EntityDeletionService

- `EntityDeletionService` is responsible for orchestrating the deletion process.
- It has the `deleteEntity()` method that takes an entity, checks if it and all its dependencies can be deleted, and then performs the deletion if eligible.
- The service utilizes two private methods: `performDeletionOnAll()` to delete all collected entities and `collectDeletionTargets()` (now part of `canBeDeleted` logic).

## Usage

1. Instantiate the desired entity (e.g., `EntityA`).
2. Create an instance of `EntityDeletionService`.
3. Call the `deleteEntity()` method of the service with the entity.

The service will output the result of the deletion process, indicating whether the deletion was successful or aborted (if any entity in the chain cannot be deleted).

## Example

\```php
require_once 'DeletableEntityInterface.php';
require_once 'Entity.php';
require_once 'EntityClasses.php';
require_once 'EntityDeletionService.php';

$entityA = new EntityA();
$deletionService = new EntityDeletionService();
$deletionService->deleteEntity($entityA);
\```

This code will attempt to delete `EntityA` and its dependencies, ensuring that all entities in the chain are eligible for deletion.
