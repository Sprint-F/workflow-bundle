<?php

namespace SprintF\Bundle\Workflow;

use SprintF\Bundle\Admin\Enum\EnumWithLabelInterface;

/**
 * Результат выполнения действия бизнес-процесса.
 */
enum ActionResult: string implements EnumWithLabelInterface
{
    case CANNOT = 'cannot';
    case PROGRESS = 'progress';
    case SUCCESS = 'success';
    case FAIL = 'fail';

    public function label(): string
    {
        return $this->value;
    }
}
