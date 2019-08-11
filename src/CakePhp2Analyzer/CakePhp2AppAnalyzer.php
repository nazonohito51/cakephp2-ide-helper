<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\CakePhp2Analyzer;

use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\BehaviorReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\FixtureReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\StructuralElements\CakePhp2App;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ModelReader;

class CakePhp2AppAnalyzer
{
    private $app;
    private $modelReaders;
    private $behaviorReaders;
    private $fixtureReaders;

    public function __construct(CakePhp2App $app)
    {
        $this->app = $app;
    }

    /**
     * @return ModelReader[]
     */
    public function getModelReaders(): array
    {
        if (!is_null($this->modelReaders)) {
            return $this->modelReaders;
        }

        $modelReaders = $this->app->getModelReaders();
        foreach ($this->app->getPlugins() as $plugin) {
            $modelReaders = array_merge($modelReaders, $plugin->getModelReaders());
        }

        return $this->modelReaders = $modelReaders;
    }

    /**
     * @return BehaviorReader[]
     */
    public function getBehaviorReaders(): array
    {
        if (!is_null($this->behaviorReaders)) {
            return $this->behaviorReaders;
        }

        $behaviorReaders = $this->app->getBehaviorReaders();
        foreach ($this->app->getPlugins() as $plugin) {
            $behaviorReaders = array_merge($behaviorReaders, $plugin->getBehaviorReaders());
        }

        return $this->behaviorReaders = $behaviorReaders;
    }

    public function searchBehaviorFromSymbol(string $behaviorSymbol): ?BehaviorReader
    {
        foreach ($this->getBehaviorReaders() as $behaviorReader) {
            if ($behaviorReader->getSymbol() === $behaviorSymbol) {
                return $behaviorReader;
            }
        }

        return null;
    }

    /**
     * @return FixtureReader[]
     */
    public function getFixtureReaders(): array
    {
        if (!is_null($this->fixtureReaders)) {
            return $this->fixtureReaders;
        }

        $fixtureReaders = $this->app->getFixtureReaders();
        foreach ($this->app->getPlugins() as $plugin) {
            $fixtureReaders = array_merge($fixtureReaders, $plugin->getFixtureReaders());
        }

        return $this->fixtureReaders = $fixtureReaders;
    }
}
