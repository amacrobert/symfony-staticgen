<?php

namespace Amacrobert\SymfonyStaticGen\Command;

use Amacrobert\SymfonyStaticGen\Service\StaticGen;
use Amacrobert\SymfonyStaticGen\Service\StaticUrlGenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

#[AsCommand(
    name: 'ssg:build',
    description: 'Build static assets',
)]
class SsgBuildCommand extends Command
{
    public function __construct(
        private StaticGen $static_gen_builder,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->static_gen_builder->setOutput($output);
        $this->static_gen_builder->buildSite();
        return Command::SUCCESS;
    }
}
