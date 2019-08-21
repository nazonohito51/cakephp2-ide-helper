<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\Command;

use CakePhp2IdeHelper\CakePhp2Analyzer\StructuralElements\CakePhp2App;
use CakePhp2IdeHelper\Generator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractIdeHelperCommand extends Command
{
    abstract protected function getCommandName(): string;
    abstract protected function getCommandDescription(): string;
    abstract protected function generateFromGenerator(Generator $generator): int;

    protected function configure(): void
    {
        $this->setName($this->getCommandName())
            ->setDescription($this->getCommandDescription())
            ->setDefinition([
                new InputOption('app-dir', null, InputOption::VALUE_REQUIRED, 'CakePHP2 app dir path'),
                new InputOption('model-dir', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Additional model dir', []),
                new InputOption('behavior-dir', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Additional behavior dir', []),
                new InputOption('plugin-dir', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Additional plugin dir', []),
                new InputOption('ignore', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Ignore file', []),
            ]);
    }

    protected function createCakePhp2App(InputInterface $input): CakePhp2App
    {
        $appDir = $input->getOption('app-dir') ?? (getcwd() . '/app');
        $app = new CakePhp2App($appDir);

        foreach ($input->getOption('model-dir') as $modelDir) {
            $app->addModelDir($modelDir);
        }
        foreach ($input->getOption('behavior-dir') as $behaviorDir) {
            $app->addBehaviorDir($behaviorDir);
        }
        foreach ($input->getOption('plugin-dir') as $pluginDir) {
            $app->addPluginDir($pluginDir);
        }
        foreach ($input->getOption('ignore') as $ignoreFile) {
            $app->addIgnoreFile($ignoreFile);
        }

        return $app;
    }

    protected function createGenerator(InputInterface $input): Generator
    {
        return new Generator(getcwd(), $this->createCakePhp2App($input));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            return $this->generateFromGenerator($this->createGenerator($input));
        } catch (\Exception $e) {
            var_dump($e);
        }

        return 1;
    }
}
