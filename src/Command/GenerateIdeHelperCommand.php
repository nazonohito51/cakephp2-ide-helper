<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\Command;

use CakePhp2IdeHelper\Generator;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateIdeHelperCommand extends AbstractIdeHelperCommand
{
    protected function getCommandName(): string
    {
        return 'generate:helper';
    }

    protected function getCommandDescription(): string
    {
        return 'generate _ide_helper.php';
    }

    protected function generateFromGenerator(Generator $generator, OutputInterface $output): int
    {
        $ideHelperContent = $generator->generateIdeHelperContent();

        $ideHelperFile = new \SplFileObject(getcwd() . '/_ide_helper.php', 'w');
        $ideHelperFile->fwrite($ideHelperContent->__toString());

        return 0;
    }
}
