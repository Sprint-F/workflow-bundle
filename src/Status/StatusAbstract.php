<?php

namespace SprintF\Bundle\Workflow\Status;

use SprintF\Bundle\Workflow\Action\ActionResult;
use SprintF\Bundle\Workflow\ActionLog\ActionLogEntryInterface;
use SprintF\Bundle\Workflow\Workflow\WorkflowEntityInterface;

/**
 * Базовый класс "Статуса" какого-либо объекта (сущности).
 */
abstract class StatusAbstract
{
    /*
     * Сущность, статус которой мы хотим получить
     */
    protected WorkflowEntityInterface $entity;

    public function getEntity(): WorkflowEntityInterface
    {
        return $this->entity;
    }

    public function setEntity(WorkflowEntityInterface $entity): static
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Метод получения статуса.
     */
    abstract public function __invoke(): bool;

    /**
     * Метод-синоним, для тех, кто не любит магии.
     */
    final public function get(): bool
    {
        return $this->__invoke();
    }

    /**
     * Вспомогательный метод: проверка, была ли ранее хотя бы одна попытка совершить такое действие?
     */
    protected function logContainsActionAttempt(string $actionClass): bool
    {
        return $this->entity->getActionLogEntries()->exists(function ($key, ActionLogEntryInterface $logEntry) use ($actionClass) {
            return $logEntry->getActionClass() === $actionClass;
        });
    }

    /**
     * Вспомогательный метод: проверка, было ли ранее хотя бы раз успешно совершено такое действие?
     */
    protected function logContainsActionSuccess(string $actionClass): bool
    {
        return $this->entity->getActionLogEntries()->exists(function ($key, ActionLogEntryInterface $logEntry) use ($actionClass) {
            return $logEntry->getActionClass() === $actionClass && ActionResult::SUCCESS === $logEntry->getResult();
        });
    }

    /**
     * Вспомогательный метод: проверка, было ли ранее хотя бы раз неуспешно совершено такое действие?
     */
    protected function logContainsActionFail(string $actionClass): bool
    {
        return $this->entity->getActionLogEntries()->exists(function ($key, ActionLogEntryInterface $logEntry) use ($actionClass) {
            return $logEntry->getActionClass() === $actionClass && ActionResult::FAIL === $logEntry->getResult();
        });
    }
}
