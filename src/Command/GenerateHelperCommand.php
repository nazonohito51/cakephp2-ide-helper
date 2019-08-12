<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\Command;

use CakePhp2IdeHelper\Generator;

class GenerateHelperCommand extends AbstractIdeHelperCommand
{
    protected function getCommandName(): string
    {
        return 'generate:helper';
    }

    protected function getCommandDescription(): string
    {
        return 'generate _ide_helper.php';
    }

    protected function generateFromGenerator(Generator $generator): int
    {
        $ideHelperContent = $generator->createIdeHelperContent();

        $ideHelperFile = new \SplFileObject(getcwd() . '/_ide_helper.php', 'w');
        $ideHelperFile->fwrite($ideHelperContent);

        return 0;
    }
}
