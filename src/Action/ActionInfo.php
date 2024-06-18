<?php

namespace SprintF\Bundle\Workflow\Action;

/**
 * DTO с информацией о действии бизнес-процесса.
 *
 * @deprecated Вся информация будет в атрибуте действия AsAction
 */
class ActionInfo
{
    public function __construct(
        /** Класс действия */
        public string $class,

        /** Наименование действия */
        public string $title,

        /** Класс контекста действия */
        public string $context,

        /** Класс формы действия */
        public ?string $form = null,

        /**
         * Можно ли применить действие к "новому" объекту, ранее не сохранявшемуся?
         *
         * @var bool|callable
         */
        public $canBeAppliedToNewEntity = false,

        /**
         * Можно ли применить действие к "существующему" объекту, уже имеющемуся в хранилище?
         *
         * @var bool|callable
         */
        public $canBeAppliedToExistingEntity = true,
    ) {
    }
}
