<?php

namespace SprintF\Bundle\Workflow\Workflow;

use SprintF\Bundle\Workflow\Action\ActionInfo;

/**
 * Базовый класс для всех классов, описывающих workflow.
 */
abstract class WorkflowAbstract implements WorkflowInterface
{
    public function getAvailableForNewEntitiesActions(): iterable
    {
        return array_filter(
            $this->getAvailableActions(),
            fn (ActionInfo $action) => is_callable($action->canBeAppliedToNewEntity) || true === $action->canBeAppliedToNewEntity
        );
    }

    public function getAvailableForExistingEntitiesActions(): iterable
    {
        return array_filter($this->getAvailableActions(), fn (ActionInfo $action) => $action->canBeAppliedToExistingEntity);
    }

    public function getActionInfoByClass(string $actionClass): ?ActionInfo
    {
        $info = array_filter($this->getAvailableActions(), fn (ActionInfo $action) => $action->class == $actionClass);

        return !empty($info) ? array_shift($info) : null;
    }
}
