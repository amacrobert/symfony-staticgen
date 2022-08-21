<?php

namespace Amacrobert\SymfonyStaticGen\Service;

use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\ExtensionInterface;
use Twig\TwigFunction;

class StaticRoutingExtension extends AbstractExtension
{
    public function __construct(private RoutingExtension $routing_extension, private string $path_prefix)
    {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('url', $this->getUrl(...), ['is_safe_callback' => $this->routing_extension->isUrlGenerationSafe(...)]),
            new TwigFunction('path', $this->getPath(...),  ['is_safe_callback' => $this->routing_extension->isUrlGenerationSafe(...)]),
        ];
    }

    public function getPath(string $name, array $parameters = [], bool $relative = false): string
    {
        $path = $this->routing_extension->getPath($name, $parameters, $relative);
        return $this->path_prefix . $path . '.html';
    }

    public function getUrl(string $name, array $parameters = [], bool $schemeRelative = false): string
    {
        $url = $this->routing_extension->getUrl($name, $parameters, $schemeRelative);
        return $url . '.html';
    }
}
