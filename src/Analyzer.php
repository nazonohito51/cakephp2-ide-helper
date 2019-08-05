<?php

namespace CakePhp2IdeHelper;

class Analyzer
{
    private $app;

    public function __construct(CakePhp2App $app)
    {
        $this->app = $app;
    }

    public function getModelReaders()
    {
        $modelReaders = $this->app->getModelReaders();

        foreach ($this->app->getPluginDirs() as $pluginDir) {
            $modelReaders = array_merge($modelReaders, $pluginDir->getModelReaders());
        }

        return $modelReaders;
    }
}
