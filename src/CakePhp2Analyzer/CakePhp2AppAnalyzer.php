<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\CakePhp2Analyzer;

use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\BehaviorReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ControllerReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\FixtureReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\PhpFileReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ShellReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\StructuralElements\CakePhp2App;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ModelReader;
use PhpParser\Node\Expr\MethodCall;

class CakePhp2AppAnalyzer
{
    private $app;
    private $modelReaders;
    private $behaviorReaders;
    private $fixtureReaders;
    private $controllerReaders;
    private $shellReaders;
    private $modelExtendsGraph;
    private $behaviorExtendsGraph;

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

    /**
     * @return ControllerReader[]
     */
    public function getControllerReaders(): array
    {
        if (!is_null($this->controllerReaders)) {
            return $this->controllerReaders;
        }

        $controllerReaders = $this->app->getControllerReaders();
        foreach ($this->app->getPlugins() as $plugin) {
            $controllerReaders = array_merge($controllerReaders, $plugin->getControllerReaders());
        }

        return $this->controllerReaders = $controllerReaders;
    }

    /**
     * @return ShellReader[]
     */
    public function getShellReaders(): array
    {
        if (!is_null($this->shellReaders)) {
            return $this->shellReaders;
        }

        $shellReaders = $this->app->getShellReaders();
        foreach ($this->app->getPlugins() as $plugin) {
            $shellReaders = array_merge($shellReaders, $plugin->getShellReaders());
        }

        return $this->shellReaders = $shellReaders;
    }

    /**
     * @return MethodCall[]
     */
    public function searchUnloadMethod(): array
    {
        $phpFiles = $this->app->getPhpFiles();
        foreach ($this->app->getPlugins() as $plugin) {
            foreach ($plugin->getPhpFiles() as $phpFile) {
                if (!in_array($phpFile, $phpFiles, true)) {
                    $phpFiles[] = $phpFile;
                }
            }
        }

        $ret = [];
        foreach ($phpFiles as $phpFile) {
            $phpFileReader = new PhpFileReader($phpFile);
            if (strpos($phpFileReader->getContent(), 'unload') !== false) {
                $methodCalls = $phpFileReader->getCallMethods('unload');
                $ret = array_merge($ret, $methodCalls);
            }
        }

        return $ret;
    }

    private function getModelReaderFromName(string $modelName): ?ModelReader
    {
        foreach ($this->getModelReaders() as $modelReader) {
            if ($modelReader->getModelName() === $modelName) {
                return $modelReader;
            }
        }

        return null;
    }

    private function getBehaviorReaderFromName(string $behaviorName): ?BehaviorReader
    {
        foreach ($this->getBehaviorReaders() as $behaviorReader) {
            if ($behaviorReader->getBehaviorName() === $behaviorName) {
                return $behaviorReader;
            }
        }

        return null;
    }

    public function getModelExtendsGraph(): ModelExtendsGraph
    {
        if (!is_null($this->modelExtendsGraph)) {
            return $this->modelExtendsGraph;
        }

        $graph = new ModelExtendsGraph();
        foreach ($this->getModelReaders() as $modelReader) {
            if ($parentModelName = $modelReader->getParentModelName()) {
                if ($parentReader = $this->getModelReaderFromName($parentModelName)) {
                    $graph->addExtends($modelReader, $parentReader);
                }
            }
        }

        return $this->modelExtendsGraph = $graph;
    }

    public function searchModelFromSymbol(string $modelSymbol): ?ModelReader
    {
        foreach ($this->getModelReaders() as $modelReader) {
            if ($modelReader->getSymbol() === $modelSymbol) {
                return $modelReader;
            }
        }

        return null;
    }

    public function getBehaviorExtendsGraph(): BehaviorExtendsGraph
    {
        if (!is_null($this->behaviorExtendsGraph)) {
            return $this->behaviorExtendsGraph;
        }

        $graph = new BehaviorExtendsGraph();
        foreach ($this->getBehaviorReaders() as $behaviorReader) {
            if ($parentBehaviorName = $behaviorReader->getParentBehaviorName()) {
                if ($parentReader = $this->getBehaviorReaderFromName($parentBehaviorName)) {
                    $graph->addExtends($behaviorReader, $parentReader);
                }
            }
        }

        return $this->behaviorExtendsGraph = $graph;
    }

    /**
     * @param ModelReader $modelReader
     * @return string[]
     */
    public function analyzeBehaviorsOf(ModelReader $modelReader): array
    {
        $isAppModelSubClass = false;
        $behaviorSymbols = $modelReader->getBehaviorSymbols();

        $parents = $this->getModelExtendsGraph()->getParents($modelReader);
        foreach ($parents as $parent) {
            if ($parent->getModelName() === 'AppModel') {
                $isAppModelSubClass = true;
                break;
            }
        }

        if ($isAppModelSubClass) {
            if ($parents[0]->getModelName() !== 'AppModel') {
                $behaviorSymbols = array_merge($parents[0]->getBehaviorSymbols(), $behaviorSymbols);
            }

            $appModel = $this->getModelReaderFromName('AppModel');
            $behaviorSymbols = array_merge($appModel->getBehaviorSymbols(), $behaviorSymbols);
        }

        return array_values(array_unique($behaviorSymbols));
    }
}
