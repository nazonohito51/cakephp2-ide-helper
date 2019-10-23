<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\Command;

use CakePhp2IdeHelper\Generator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateControllerDocCommand extends AbstractIdeHelperCommand
{
    private $ignoreGit = false;
    private $gitManagedFiles = [];

    protected function getCommandName(): string
    {
        return 'generate:controller';
    }

    protected function getCommandDescription(): string
    {
        return 'generate controller phpdoc';
    }

    protected function configure(): void
    {
        parent::configure();
        $this->addOption('git-root', null, InputOption::VALUE_REQUIRED, 'Git root dir', null);
        $this->addOption('ignore-git', null, InputOption::VALUE_NONE, 'Update all models(with git unmanaged file)');
    }

    private function setGitManagedFiles(InputInterface $input): void
    {
        if (($this->ignoreGit = $input->getOption('ignore-git')) === false) {
            $gitRootDir = $input->getOption('git-root') ?? getcwd();
            $gitRootDir = substr($gitRootDir, -1) === '/' ? $gitRootDir : "{$gitRootDir}/";

            if (count(glob($gitRootDir . '.git/', GLOB_ONLYDIR)) !== 1) {
                throw new \InvalidArgumentException('cakephp2-ide-helper require git root dir.');
            }

            $currentDir = getcwd();
            chdir($gitRootDir);
            exec('git ls-files', $files, $return);

            if ($return !== 0) {
                throw new \InvalidArgumentException('cakephp2-ide-helper require git root dir.');
            }
            foreach ($files as $file) {
                $this->gitManagedFiles[] = realpath($file);
            }

            chdir($currentDir);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->setGitManagedFiles($input);

        return parent::execute($input, $output);
    }

    protected function generateFromGenerator(Generator $generator, OutputInterface $output): int
    {
        foreach ($generator->generateControllerDocEntries() as $updateControllerDocEntry) {
            if ($this->ignoreGit || in_array($updateControllerDocEntry->getControllerPath(), $this->gitManagedFiles, true)) {
                $updateControllerDocEntry->update();
            }
        }

        return 0;
    }
}
