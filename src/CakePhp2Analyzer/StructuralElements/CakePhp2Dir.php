<?php

namespace CakePhp2IdeHelper\CakePhp2Analyzer\StructuralElements;

use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\BehaviorReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\FixtureReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ModelReader;

abstract class CakePhp2Dir
{
    abstract public function isPlugin(): bool;
    abstract public function getPluginName(): ?string;
    protected $appDir;

    /**
     * @var string[]
     */
    protected $modelDirs = [];

    public function __construct(string $appDir)
    {
        if (!is_dir($appDir = realpath($appDir))) {
            throw new \InvalidArgumentException('app dir is invalid: ' . $appDir);
        }

        $this->appDir = $appDir;

        $this->modelDirs[] = $this->getModelDirPath();
    }

    public function getModelDirPath(): string
    {
        return $this->appDir . '/Model';
    }

    public function getFixtureDirPath(): string
    {
        return $this->appDir . '/Test/Fixture';
    }

    public function addModelDir(string $modelDir): void
    {
        if (!is_dir($modelDir)) {
            throw new \InvalidArgumentException();
        }

        $this->modelDirs[] = $modelDir;
    }

    /**
     * @return string[]
     */
    public function getModelFiles(): array
    {
        $ret = [];
        foreach ($this->modelDirs as $modelDir) {
            foreach (glob($modelDir . '/*.php') as $path) {
                $ret[] = $path;
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
    public function getBehaviorFiles(): array
    {
        $ret = [];
        foreach ($this->modelDirs as $modelDir) {
            foreach (glob($modelDir . '/Behavior/*.php') as $path) {
                $ret[] = $path;
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

    private function getFixtureFiles(): array
    {
        $ret = [];
        foreach (glob($this->getFixtureDirPath() . '/*.php') as $path) {
            $ret[] = $path;
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
}
