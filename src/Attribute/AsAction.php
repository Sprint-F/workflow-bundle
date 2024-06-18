<?php

namespace SprintF\Bundle\Workflow\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsAction
{
    public function __construct(
        public ?string $description = null,
    ) {
    }
}
