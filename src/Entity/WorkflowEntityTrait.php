<?php

namespace SprintF\Bundle\Workflow\Entity;

use Doctrine\Common\Collections\Collection;
use SprintF\Bundle\Workflow\ActionLog\ActionLogEntryInterface;

trait WorkflowEntityTrait /* implements WorkflowEntityInterface */
{
    public function getEntityClass(): string
    {
        return static::class;
    }

    public function getEntityId(): int|string|null
    {
        return $this->getId();
    }

    public function setEntityId(int|string|null $id): self
    {
        return $this->setId($id);
    }

    public static function getActionLogEntryClass(): string
    {
        return get_called_class().'ActionLog';
    }

    protected Collection $actionLogEntries;

    public function getActionLogEntries(): Collection
    {
        return $this->actionLogEntries;
    }

    public function addActionLogEntry(ActionLogEntryInterface $logEntry): static
    {
        if (!$this->actionLogEntries->contains($logEntry)) {
            $this->actionLogEntries[] = $logEntry;
        }

        return $this;
    }
}
