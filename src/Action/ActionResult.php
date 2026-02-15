<?php

namespace SprintF\Bundle\Workflow\Action;

use SprintF\ValueObjects\Enum\LabeledEnum;

/**
 * Результат выполнения действия бизнес-процесса.
 */
enum ActionResult: string implements LabeledEnum
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
