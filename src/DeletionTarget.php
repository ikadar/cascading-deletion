<?php /** @noinspection PhpUnused */

namespace IKadar\CascadingDeletion;

class DeletionTarget implements DeletionTargetInterface
{
    /**
     * @var int
     */
    private int $entityId;
    /**
     * @var EntityRepositoryInterface
     */
    private EntityRepositoryInterface $repository;
    /**
     * @var bool|mixed|null
     */
    protected ?bool $isDeletable;
    /**
     * @var string|mixed
     */
    protected string $message;

    // Indicate that the cause for undeletability comes from within the entity itself or from a dependency.
    protected bool $unDeletabilityIsInternal;

    protected bool $checkingStarted = false;

    /**
     * @param int $entityId
     * @param EntityRepositoryInterface $repository
     * @param bool|null $isDeletable
     * @param string|null $message
     */
    public function __construct(
        int $entityId,
        EntityRepositoryInterface $repository,
        ?bool $isDeletable = null,
        string $message = null
    )
    {
        $this->entityId = $entityId;
        $this->repository = $repository;
        $this->isDeletable = $isDeletable;
        $this->message = $message ?: "";
    }

    /**
     * @return int
     */
    public function getEntityId():int
    {
        return $this->entityId;
    }

    /**
     * @return EntityRepositoryInterface
     */
    public function getRepository():EntityRepositoryInterface
    {
        return $this->repository;
    }

    /**
     * @param bool $isDeletable
     */
    public function setIsDeletable(?bool $isDeletable): void
    {
        $this->isDeletable = $isDeletable;
    }

    /**
     * @return bool|null
     */
    public function getIsDeletable(): ?bool
    {
        return $this->isDeletable;
    }

    /**
     * @return string
     */
    public function getMessage():string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @param $deletionTargets
     * @return DeletionTargetInterface[]
     */
    public function getUnDeletableDependencies($deletionTargets): array
    {
        return $this
            ->getRepository()
            ->getUnDeletableTargets(
                $this->getEntityId(),
                $deletionTargets
            );
    }

    /**
     * @param $unDeletableTargets
     * @return DeletionTargetInterface
     */
    public function disableDeletion($unDeletableTargets): DeletionTargetInterface
    {
        // Undeletability is internal if the undeletable entity is the first in the chain
        $topUndeletableEntity = $unDeletableTargets[array_key_first($unDeletableTargets)];
        $unDeletabilityIsInternal = ($this->getEntityId() === $topUndeletableEntity->getEntityId());

        $this->setUnDeletabilityIsInternal($unDeletabilityIsInternal);
        $this->setIsDeletable(false);
        return $this;
    }

    /**
     * @param bool $checkingStarted
     */
    public function setCheckingStarted(bool $checkingStarted): void
    {
        $this->checkingStarted = $checkingStarted;
    }

    /**
     * @return bool
     */
    public function isCheckingStarted(): bool
    {
        return $this->checkingStarted;
    }

    /**
     * @return bool
     */
    public function isUnDeletabilityIsInternal(): bool
    {
        return $this->unDeletabilityIsInternal;
    }

    /**
     * @param bool $unDeletabilityIsInternal
     */
    public function setUnDeletabilityIsInternal(bool $unDeletabilityIsInternal): void
    {
        $this->unDeletabilityIsInternal = $unDeletabilityIsInternal;
    }

}
