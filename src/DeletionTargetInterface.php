<?php /** @noinspection PhpUnused */

namespace IKadar\CascadingDeletion;

interface DeletionTargetInterface
{
    public function getEntityId();

    public function getRepository();

    /**
     * @param bool $isDeletable
     */
    public function setIsDeletable(bool $isDeletable): void;

    public function getIsDeletable();

    /**
     * @return string
     */
    public function getMessage():string;

    /**
     * @param string $message
     */
    public function setMessage(string $message): void;

    /**
     * @param $unDeletableTargets
     * @return DeletionTargetInterface
     */
    public function disableDeletion($unDeletableTargets): DeletionTargetInterface;

}
