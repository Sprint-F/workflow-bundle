<?php

namespace SprintF\Bundle\Workflow;

use Doctrine\Common\Collections\Collection;

interface WorkflowEntityInterface
{
    /**
     * Мета-объект бизнес-процесса.
     */
    public static function getWorkflow(): WorkflowInterface;

    /**
     * Имя класса сущности, у которой есть лог действий. Почти всегда это будет именно класс в смысле PHP-класса.
     */
    public function getEntityClass(): string;

    /**
     * Уникальный идентификатор сущности внутри ее класса.
     * Может быть строкой или числом.
     * null предусматривается на случай "новой" сущности, еще не получившей ID.
     */
    public function getEntityId(): null|int|string;

    /**
     * Вообще говоря, этот метод не нужен. Но он требуется нам для фикса поведения Doctrine в отдельных случаях,
     * когда она присваивает идентификатор по факту не сохраненной записи...
     */
    public function setEntityId(null|int|string $id): self;

    /**
     * Класс записи в логе действий.
     *
     * @template TActionLogEntry
     *
     * @return class-string<TActionLogEntry>
     */
    public static function getActionLogEntryClass(): string;

    /**
     * Коллекция записей лога действий данной сущности.
     *
     * @template TActionLogEntry
     *
     * @return Collection|TActionLogEntry[]
     */
    public function getActionLogEntries(): Collection;

    /**
     * Добавление к логу действий новой записи о действии.
     */
    public function addActionLogEntry(ActionLogEntryInterface $logEntry): static;
}
