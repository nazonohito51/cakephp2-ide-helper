<?php

namespace CakePhp2IdeHelper;

abstract class CakePhp2Dir
{
    /**
     * @return bool
     */
    abstract public function isPlugin();

    /**
     * @return string|null
     */
    abstract public function getPluginName();

    /**
     * @var string
     */
    protected $appDir;

    /**
     * @var string[]
     */
    protected $modelDirs = array();

    /**
     * @param string $appDir
     */
    public function __construct($appDir)
    {
        if (!is_dir($appDir = realpath($appDir))) {
            throw new \InvalidArgumentException('app dir is invalid: ' . $appDir);
        }

        $this->appDir = $appDir;

        $this->modelDirs[] = $this->getModelDirPath();
    }

    /**
     * @return string
     */
    public function getModelDirPath()
    {
        return $this->appDir . '/Model';
    }

    /**
     * @param string $modelDir
     */
    public function addModelDir($modelDir)
    {
        if (!is_dir($modelDir)) {
            throw new \InvalidArgumentException();
        }

        $this->modelDirs[] = $modelDir;
    }

    /**
     * @return string[]
     */
    public function getModelFiles()
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
    public function getModelReaders()
    {
        $modelReaders = array();
        foreach ($this->getModelFiles() as $modelFile) {
            $modelReaders[] = new ModelReader($modelFile);
        }

        return $modelReaders;
    }
}
