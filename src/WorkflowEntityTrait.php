<?php

namespace SprintF\Bundle\Workflow;

use Doctrine\Common\Collections\Collection;

trait WorkflowEntityTrait /* implements WorkflowEntityInterface */
{
    public function getEntityClass(): string
    {
        return static::class;
    }

    public function getEntityId(): null|int|string
    {
        return $this->getId();
    }

    public function setEntityId(null|int|string $id): self
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
