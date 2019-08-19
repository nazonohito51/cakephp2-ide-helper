<?php
declare(strict_types=1);

namespace Tests\Unit\CakePhp2Analyzer\Readers;

use CakePhp2IdeHelper\CakePhp2Analyzer\BehaviorExtendsGraph;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\BehaviorReader;
use Tests\TestCase;

class BehaviorExtendsGraphTest extends TestCase
{
    public function testGetParents()
    {
        $behavior1 = new BehaviorReader($this->fixtureAppPath('/Model/Behavior/SomeBehavior1Behavior.php'));
        $behavior2 = new BehaviorReader($this->fixtureAppPath('/Model/Behavior/SomeBehavior2Behavior.php'));
        $behavior3 = new BehaviorReader($this->fixtureAppPath('/Plugin/SomePlugin1/Model/Behavior/SomeBehavior3Behavior.php'));
        $graph = new BehaviorExtendsGraph();

        $graph->addExtends($behavior1, $behavior2);
        $graph->addExtends($behavior2, $behavior3);

        $this->assertSame(['SomeBehavior2Behavior', 'SomeBehavior3Behavior'], array_map(function (BehaviorReader $behaviorReader) {
            return $behaviorReader->getBehaviorName();
        }, $graph->getParents($behavior1)));
        $this->assertSame(['SomeBehavior3Behavior'], array_map(function (BehaviorReader $behaviorReader) {
            return $behaviorReader->getBehaviorName();
        }, $graph->getParents($behavior2)));
        $this->assertSame([], array_map(function (BehaviorReader $behaviorReader) {
            return $behaviorReader->getBehaviorName();
        }, $graph->getParents($behavior3)));
    }
}
