<?php

namespace CakePhp2IdeHelper;

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
        $ret = array();
        foreach ($this->modelDirs as $modelDir) {
            foreach (glob($modelDir . '/*') as $path) {
                if (is_file($path)) {
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
        $modelReaders = array();
        foreach ($this->getModelFiles() as $modelFile) {
            $modelReaders[] = new ModelReader($modelFile);
        }

        return $modelReaders;
    }
}
