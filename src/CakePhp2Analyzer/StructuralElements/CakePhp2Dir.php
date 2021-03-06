<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\CakePhp2Analyzer\StructuralElements;

use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\BehaviorReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ControllerReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\FixtureReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ModelReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\PhpFileReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ShellReader;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

abstract class CakePhp2Dir
{
    abstract public function isPlugin(): bool;
    abstract public function getPluginName(): ?string;

    protected $appDir;

    /**
     * @var string[]
     */
    protected $modelDirs = [];

    /**
     * @var string[]
     */
    protected $controllerDirs = [];

    /**
     * @var string[]
     */
    protected $shellDirs = [];

    /**
     * @var string[]
     */
    private $ignoreFiles = [];

    public function __construct(string $appDir)
    {
        if (!is_dir($appDir = realpath($appDir))) {
            throw new \InvalidArgumentException('app dir is invalid: ' . $appDir);
        }

        $this->appDir = $appDir;
        $this->modelDirs[] = $this->getModelDirPath();
        $this->controllerDirs[] = $this->getControllerDirPath();
        $this->shellDirs[] = $this->getShellDirPath();
    }

    public function addIgnoreFile(string $ignoreFile): void
    {
        if (!is_file($ignoreFile = realpath($ignoreFile))) {
            throw new \InvalidArgumentException('ignore file is invalid: ' . $ignoreFile);
        }

        $this->ignoreFiles[] = $ignoreFile;
    }

    public function getIgnoreFiles(): array
    {
        return $this->ignoreFiles;
    }

    private function isIgnoreFile(string $file): bool
    {
        foreach ($this->getIgnoreFiles() as $ignoreFile) {
            if ($file === $ignoreFile) {
                return true;
            }
        }

        return false;
    }

    protected function getModelDirPath(): string
    {
        return $this->appDir . '/Model';
    }

    protected function getFixtureDirPath(): string
    {
        return $this->appDir . '/Test/Fixture';
    }

    protected function getControllerDirPath(): string
    {
        return $this->appDir . '/Controller';
    }

    protected function getShellDirPath(): string
    {
        return $this->appDir . '/Console/Command';
    }

    public function addModelDir(string $modelDir): void
    {
        $modelDir = realpath($modelDir);
        if (!is_dir($modelDir)) {
            throw new \InvalidArgumentException('model dir is invalid:' . $modelDir);
        }

        $this->modelDirs[] = $modelDir;
    }

    public function addControllerDir(string $controllerDir): void
    {
        $controllerDir = realpath($controllerDir);
        if (!is_dir($controllerDir)) {
            throw new \InvalidArgumentException('controller dir is invalid:' . $controllerDir);
        }

        $this->controllerDirs[] = $controllerDir;
    }

    public function addShellDir(string $shellDir): void
    {
        $shellDir = realpath($shellDir);
        if (!is_dir($shellDir)) {
            throw new \InvalidArgumentException('shell dir is invalid:' . $shellDir);
        }

        $this->shellDirs[] = $shellDir;
    }

    /**
     * @return string[]
     */
    protected function getModelFiles(): array
    {
        $ret = [];
        foreach ($this->modelDirs as $modelDir) {
            foreach (glob($modelDir . '/*.php') as $path) {
                if (!$this->isIgnoreFile($path)) {
                    $ret[] = $path;
                }
            }
        }

        return $ret;
    }

    /**
     * @return ModelReader[]
     */
    public function getModelReaders(): array
    {
        $modelReaders = [];
        foreach ($this->getModelFiles() as $modelFile) {
            $modelReaders[] = new ModelReader($modelFile);
        }

        return $modelReaders;
    }

    /**
     * @return string[]
     */
    protected function getBehaviorFiles(): array
    {
        $ret = [];
        foreach ($this->modelDirs as $modelDir) {
            foreach (glob($modelDir . '/Behavior/*.php') as $path) {
                if (!$this->isIgnoreFile($path)) {
                    $ret[] = $path;
                }
            }
        }

        return $ret;
    }

    /**
     * @return BehaviorReader[]
     */
    public function getBehaviorReaders(): array
    {
        $behaviorReaders = [];
        foreach ($this->getBehaviorFiles() as $behaviorFile) {
            $behaviorReaders[] = new BehaviorReader($behaviorFile);
        }

        return $behaviorReaders;
    }

    protected function getFixtureFiles(): array
    {
        $ret = [];
        foreach (glob($this->getFixtureDirPath() . '/*.php') as $path) {
            if (!$this->isIgnoreFile($path)) {
                $ret[] = $path;
            }
        }

        return $ret;
    }

    /**
     * @return FixtureReader[]
     */
    public function getFixtureReaders(): array
    {
        $fixtureReaders = [];
        foreach ($this->getFixtureFiles() as $fixtureFile) {
            $fixtureReaders[] = new FixtureReader($fixtureFile);
        }

        return $fixtureReaders;
    }

    protected function getControllerFiles(): array
    {
        $ret = [];
        foreach ($this->controllerDirs as $controllerDir) {
            foreach (glob($controllerDir . '/*.php') as $path) {
                if (!$this->isIgnoreFile($path)) {
                    $ret[] = $path;
                }
            }
        }

        return $ret;
    }

    /**
     * @return ControllerReader[]
     */
    public function getControllerReaders(): array
    {
        $controllerReaders = [];
        foreach ($this->getControllerFiles() as $controllerFile) {
            $controllerReaders[] = new ControllerReader($controllerFile);
        }

        return $controllerReaders;
    }

    protected function getShellFiles(): array
    {
        $ret = [];
        foreach ($this->shellDirs as $shellDir) {
            foreach (glob($shellDir . '/*.php') as $path) {
                if (!$this->isIgnoreFile($path)) {
                    $ret[] = $path;
                }
            }
        }

        return $ret;
    }

    /**
     * @return ShellReader[]
     */
    public function getShellReaders(): array
    {
        $shellReaders = [];
        foreach ($this->getShellFiles() as $shellFile) {
            $shellReaders[] = new ShellReader($shellFile);
        }

        return $shellReaders;
    }

    public function getPhpFiles()
    {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->appDir));

        $phpFiles = [];
        foreach ($iterator as $fileInfo) {
            /** @var $fileInfo \SplFileInfo */
            if ($fileInfo->getExtension() === 'php') {
                if (!$this->isIgnoreFile($fileInfo->getRealPath())) {
                    $phpFiles[] = $fileInfo->getRealPath();
                }
            }
        }

        return $phpFiles;
    }

    /**
     * @return PhpFileReader[]
     */
    public function getPhpFileReaders(): array
    {
        $phpFileReaders = [];
        foreach ($this->getPhpFiles() as $phpFile) {
            $phpFileReaders = new PhpFileReader($phpFile);
        }

        return $phpFileReaders;
    }
}
