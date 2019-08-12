<?php

namespace Tests\Unit\CakePhp2Analyzer\StructuralElements;

use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\BehaviorReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\FixtureReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ModelReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\StructuralElements\CakePhp2App;
use CakePhp2IdeHelper\CakePhp2Analyzer\StructuralElements\CakePhp2Plugin;
use Tests\TestCase;

class CakePhp2PluginTest extends TestCase
{
    public function testPluginFunctions()
    {
        $app = new CakePhp2Plugin($this->fixtureAppPath('Plugin/SomePlugin1'), 'SomePlugin1');

        $this->assertTrue($app->isPlugin());
        $this->assertSame('SomePlugin1', $app->getPluginName());
    }

    public function testModelReaders()
    {
        $app = new CakePhp2Plugin($this->fixtureAppPath('Plugin/SomePlugin1'), 'SomePlugin1');
        $app->addModelDir($this->fixtureAppPath('AdditionalModel'));

        $modelReaders = $app->getModelReaders();

        $this->assertCount(3, $modelReaders);
        $this->assertSame(['SomeModel3', 'SomeModel4', 'SomeModel5'], array_map(function (ModelReader $modelReader) {
            return $modelReader->getModelName();
        }, $modelReaders));
    }

    public function testBehaviorReaders()
    {
        $app = new CakePhp2Plugin($this->fixtureAppPath('Plugin/SomePlugin1'), 'SomePlugin1');

        $behaviorReaders = $app->getBehaviorReaders();

        $this->assertCount(1, $behaviorReaders);
        $this->assertSame(['SomeBehavior3'], array_map(function (BehaviorReader $behaviorReader) {
            return $behaviorReader->getBehaviorName();
        }, $behaviorReaders));
    }

    public function testFixtureReaders()
    {
        $app = new CakePhp2Plugin($this->fixtureAppPath('Plugin/SomePlugin1'), 'SomePlugin1');

        $fixtureReaders = $app->getFixtureReaders();

        $this->assertCount(0, $fixtureReaders);
    }
}
