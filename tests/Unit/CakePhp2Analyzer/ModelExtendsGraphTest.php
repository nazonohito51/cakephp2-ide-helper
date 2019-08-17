<?php
declare(strict_types=1);

namespace Tests\Unit\CakePhp2Analyzer\Readers;

use CakePhp2IdeHelper\CakePhp2Analyzer\ModelExtendsGraph;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ModelReader;
use Tests\TestCase;

class ModelExtendsGraphTest extends TestCase
{
    public function provideGraph()
    {
        $model1 = new ModelReader($this->fixtureAppPath('/Model/SomeModel1.php'));
        $model2 = new ModelReader($this->fixtureAppPath('/Model/SomeModel2.php'));
        $model3 = new ModelReader($this->fixtureAppPath('/Model/SomeModel3.php'));
        $model4 = new ModelReader($this->fixtureAppPath('/Model/SomeModel4.php'));
        $graph = new ModelExtendsGraph();

        $graph->addExtends($model1, $model2);
        $graph->addExtends($model2, $model4);
        $graph->addExtends($model3, $model4);

        return [
            [$graph]
        ];
    }

    public function testGetParents()
    {
        $model1 = new ModelReader($this->fixtureAppPath('/Model/SomeModel1.php'));
        $model2 = new ModelReader($this->fixtureAppPath('/Model/SomeModel2.php'));
        $model3 = new ModelReader($this->fixtureAppPath('/Plugin/SomePlugin1/Model/SomeModel3.php'));
        $model4 = new ModelReader($this->fixtureAppPath('/Plugin/SomePlugin1/Model/SomeModel4.php'));
        $graph = new ModelExtendsGraph();

        $graph->addExtends($model1, $model2);
        $graph->addExtends($model2, $model4);
        $graph->addExtends($model3, $model4);

        $this->assertSame(['SomeModel2', 'SomeModel4'], array_map(function (ModelReader $modelReader) {
            return $modelReader->getModelName();
        }, $graph->getParents($model1)));
        $this->assertSame(['SomeModel4'], array_map(function (ModelReader $modelReader) {
            return $modelReader->getModelName();
        }, $graph->getParents($model2)));
        $this->assertSame(['SomeModel4'], array_map(function (ModelReader $modelReader) {
            return $modelReader->getModelName();
        }, $graph->getParents($model3)));
        $this->assertSame([], array_map(function (ModelReader $modelReader) {
            return $modelReader->getModelName();
        }, $graph->getParents($model4)));
    }

//    public function testGetChildren()
//    {
//        $model1 = new ModelReader($this->fixtureAppPath('/Model/SomeModel1.php'));
//        $model2 = new ModelReader($this->fixtureAppPath('/Model/SomeModel2.php'));
//        $model3 = new ModelReader($this->fixtureAppPath('/Plugin/SomePlugin1/Model/SomeModel3.php'));
//        $model4 = new ModelReader($this->fixtureAppPath('/Plugin/SomePlugin1/Model/SomeModel4.php'));
//        $graph = new ModelExtendsGraph();
//
//        $graph->addExtends($model1, $model2);
//        $graph->addExtends($model2, $model4);
//        $graph->addExtends($model3, $model4);
//
//        $this->assertSame(['SomeModel3' => [], 'SomeModel2' => ['SomeModel1' => []]], array_map(function (ModelReader $modelReader) {
//            return $modelReader->getModelName();
//        }, $graph->getChildren($model4)));
//        $this->assertSame([], array_map(function (ModelReader $modelReader) {
//            return $modelReader->getModelName();
//        }, $graph->getChildren($model3)));
//        $this->assertSame(['SomeModel1' => []], array_map(function (ModelReader $modelReader) {
//            return $modelReader->getModelName();
//        }, $graph->getChildren($model2)));
//        $this->assertSame([], array_map(function (ModelReader $modelReader) {
//            return $modelReader->getModelName();
//        }, $graph->getChildren($model1)));
//    }
}
