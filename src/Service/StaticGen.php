<?php

namespace Amacrobert\SymfonyStaticGen\Service;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class StaticGen
{
    private ?OutputInterface $output = null;

    public function __construct(
        private RouterInterface $router,
        private ContainerInterface $service_container,
        private Filesystem $filesystem,
        private string $build_path,
    )
    {}

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function buildSite()
    {
        $paths = $this->getRoutes();

        foreach ($paths as $path) {
            $this->buildPageForPath($path);
        }
    }

    public function buildPageForPath($path)
    {
        $http_kernel = $this->service_container->get('http_kernel');
        $request = Request::create($path, Request::METHOD_GET, [], [], [], $_SERVER);

        try {
            $response = $http_kernel->handle($request);
        }
        catch (\Throwable $t) {
            $this->output?->writeln(
                sprintf("<error>Error processing \"%s\":\n\n%s</error>", $path, $t->getMessage())
            );
            return;
        }

        $content = $response->getContent();
        $file = $this->build_path . $path . '.html';
        $this->filesystem->dumpFile($file, $content);
        $this->output?->writeln($file);
    }

    public function getRoutes(): array
    {
        $routes = $this->service_container->getParameter('symfony_static_gen.routes');
        return $routes;
    }
}
