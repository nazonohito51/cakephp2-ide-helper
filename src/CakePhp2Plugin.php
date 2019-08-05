<?php

namespace CakePhp2IdeHelper;

class CakePhp2Plugin extends CakePhp2Dir
{
    private $pluginName;

    public function __construct($appDir, $pluginName)
    {
        parent::__construct($appDir);
        $this->pluginName = $pluginName;
    }

    /**
     * @return bool
     */
    public function isPlugin()
    {
        return true;
    }

    public function getPluginName()
    {
        return $this->pluginName;
    }

    /**
     * @return ModelReader[]
     */
    public function getModelReaders()
    {
        $modelReaders = [];
        foreach ($this->getModelFiles() as $modelFile) {
            $modelReaders[] = new ModelReader($modelFile, $this->getPluginName());
        }

        return $modelReaders;
    }
}
