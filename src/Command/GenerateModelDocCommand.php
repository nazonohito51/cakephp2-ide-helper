<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\Command;

use CakePhp2IdeHelper\Generator;

class GenerateModelDocCommand extends AbstractIdeHelperCommand
{
    protected function getCommandName(): string
    {
        return 'generate:model';
    }

    protected function getCommandDescription(): string
    {
        return 'generate model phpdoc';
    }

    protected function generateFromGenerator(Generator $generator): int
    {
        foreach ($generator->generateModelDocEntries() as $updateModelDocEntry) {
            $updateModelDocEntry->update();
        }

        return 0;
    }
}
