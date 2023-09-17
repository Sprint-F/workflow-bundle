<?php

namespace SprintF\Bundle\Workflow;

/**
 * DTO с информацией о статусе бизнес-процесса.
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
