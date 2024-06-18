<?php

namespace SprintF\Bundle\Workflow\Action;

use SprintF\Bundle\Admin\Enum\EnumWithLabelInterface;

/**
 * Результат выполнения действия бизнес-процесса.
 *
 * @todo: Разорвать циклическую связь с админ-панелью!
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
