<?php

namespace SprintF\Bundle\Workflow;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SprintFWorkflowBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return dirname(__DIR__);
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');

        $builder->registerAttributeForAutoconfiguration(AsCommand::class, static function (ChildDefinition $definition, AsCommand $attribute, \ReflectionClass $reflector): void {
            $definition->addTag('workflow.action');
        });
    }
}
