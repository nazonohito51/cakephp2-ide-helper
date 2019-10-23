<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\Command;

use CakePhp2IdeHelper\Generator;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateMetaCommand extends AbstractIdeHelperCommand
{
    protected function getCommandName(): string
    {
        return 'generate:meta';
    }

    protected function getCommandDescription(): string
    {
        return 'generate .phpstorm.meta.php';
    }

    protected function generateFromGenerator(Generator $generator, OutputInterface $output): int
    {
        $metaFileContent = $generator->generatePhpStormMetaFileContent();

        $phpstormMetaFile = new \SplFileObject(getcwd() . '/.phpstorm.meta.php', 'w');
        $phpstormMetaFile->fwrite($metaFileContent);

        return 0;
    }
}
