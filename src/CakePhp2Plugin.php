<?php

namespace CakePhp2IdeHelper;

class CakePhp2Plugin extends CakePhp2Dir
{
    private $pluginName;

    public function __construct(string $appDir, string $pluginName)
    {
        parent::__construct($appDir);
        $this->pluginName = $pluginName;
    }

    public function isPlugin(): bool
    {
        return true;
    }

    public function getPluginName(): ?string
    {
        return $this->pluginName;
    }

    /**
     * @return ModelReader[]
     */
    public function getModelReaders(): array
    {
        $modelReaders = array();
        foreach ($this->getModelFiles() as $modelFile) {
            $modelReaders[] = new ModelReader($modelFile, $this->getPluginName());
        }

        return $modelReaders;
    }
}
