<?php

namespace Amacrobert\SymfonyStaticGen;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;


class SymfonyStaticGenBundle extends AbstractBundle
{
    /**
     * @todo: Override twig functions defined in twig-bridge
     * vendor/symfony/twig-bridge/Extension/RoutingExtension.php
     *  - path
     *  - url
     * vendor/symfony/twig-bridge/Extension/HttpFoundationExtension.php
     *  - absolute_url
     *  - relative_path
     * vendor/symfony/twig-bridge/Extension/AssetExtension.php
     *  - asset
     */
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('output')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('build_path')
                            ->defaultValue('%kernel.project_dir%/build')
                            ->info('Directory to build the static site in')
                        ->end()
                    ->end()
                ->end()
                    ->arrayNode('routes')
                    ->scalarPrototype()->end()
                ->end()
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        // load an XML, PHP or Yaml file
        $container->import('../config/services.xml');

        // Resolve %kernel.project_dir% from default config
        $config = $builder->getParameterBag()->resolveValue($config);

        // Set any config-defined routes for the main static route provider
        $builder->setParameter('symfony_static_gen.routes', $config['routes']);

        $definition = $builder->getDefinition('static_gen.builder');
        $definition->addArgument($config['output']['build_path']);
    }
}
