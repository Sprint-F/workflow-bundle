<?php

namespace SprintF\Bundle\Workflow\Status;

/**
 * DTO с информацией о статусе бизнес-процесса.
 * @deprecated Вся информация будет в атрибуте
 */
class StatusInfo
{
    public function __construct(
        /** Класс статуса */
        public string $class,

        /** Наименование статуса */
        public string $title,
    ) {
    }
}
