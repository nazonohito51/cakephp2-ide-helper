<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\CakePhp2Analyzer\StructuralElements;

use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\BehaviorReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ModelReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\StructuralElements\CakePhp2Dir;

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
        $modelReaders = [];
        foreach ($this->getModelFiles() as $modelFile) {
            $modelReaders[] = new ModelReader($modelFile, $this->getPluginName());
        }

        return $modelReaders;
    }

    public function getBehaviorReaders(): array
    {
        $behaviorReaders = [];
        foreach ($this->getBehaviorFiles() as $behaviorFile) {
            $behaviorReaders[] = new BehaviorReader($behaviorFile, $this->getPluginName());
        }

        return $behaviorReaders;
    }
}
