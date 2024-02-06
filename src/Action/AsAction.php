<?php

namespace SprintF\Bundle\Workflow\Action;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AsAction
{
    public function __construct(
        public ?string $description = null,
    )
    {}
}
