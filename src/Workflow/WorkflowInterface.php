<?php

namespace SprintF\Bundle\Workflow\Workflow;

use SprintF\Bundle\Workflow\Action\ActionInfo;
use SprintF\Bundle\Workflow\Status\StatusInfo;

/**
 * Общий интерфейс для всех классов, описывающих workflow.
 */
interface WorkflowInterface
{
    /**
     * Список действий, входящих в данный flow.
     *
     * @return iterable|ActionInfo[]
     */
    public function getAvailableActions(): iterable;

    /**
     * Список действий, входящих в данный flow и применимых к "новым" сущностям
     *
     * @return iterable|ActionInfo[]
     */
    public function getAvailableForNewEntitiesActions(): iterable;

    /**
     * Список действий, входящих в данный flow и применимых к существующим сущностям
     *
     * @return iterable|ActionInfo[]
     */
    public function getAvailableForExistingEntitiesActions(): iterable;

    /**
     * Информация о действии по его классу.
     */
    public function getActionInfoByClass(string $actionClass): ?ActionInfo;

    /**
     * Список статусов, входящих в данный flow.
     *
     * @return iterable|StatusInfo[]
     */
    public function getAvailableStatuses(): iterable;
}
