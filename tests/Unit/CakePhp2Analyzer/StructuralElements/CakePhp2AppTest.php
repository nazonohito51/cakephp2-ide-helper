<?php
declare(strict_types=1);

namespace Tests\Unit\CakePhp2Analyzer\StructuralElements;

use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\BehaviorReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\FixtureReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ModelReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\StructuralElements\CakePhp2App;
use Tests\TestCase;

class CakePhp2AppTest extends TestCase
{
    public function testPluginFunctions()
    {
        $app = new CakePhp2App($this->fixtureAppPath());

        $this->assertFalse($app->isPlugin());
        $this->assertNull($app->getPluginName());
        $this->assertCount(1, $app->getPlugins());
        $this->assertSame('SomePlugin1', $app->getPlugins()[0]->getPluginName());
    }

    public function testModelReaders()
    {
        $app = new CakePhp2App($this->fixtureAppPath());
        $app->addModelDir($this->fixtureAppPath('AdditionalModel'));

        $modelReaders = $app->getModelReaders();

        $this->assertCount(4, $modelReaders);
        $this->assertSame(['AppModel', 'SomeModel1', 'SomeModel2', 'SomeModel5'], array_map(function (ModelReader $modelReader) {
            return $modelReader->getModelName();
        }, $modelReaders));
    }

    public function testBehaviorReaders()
    {
        $app = new CakePhp2App($this->fixtureAppPath());
        $app->addModelDir($this->fixtureAppPath('AdditionalModel'));

        $behaviorReaders = $app->getBehaviorReaders();

        $this->assertCount(2, $behaviorReaders);
        $this->assertSame(['SomeBehavior1Behavior', 'SomeBehavior2Behavior'], array_map(function (BehaviorReader $behaviorReader) {
            return $behaviorReader->getBehaviorName();
        }, $behaviorReaders));
    }

    public function testFixtureReaders()
    {
        $app = new CakePhp2App($this->fixtureAppPath());
        $app->addModelDir($this->fixtureAppPath('AdditionalModel'));

        $fixtureReaders = $app->getFixtureReaders();

        $this->assertCount(2, $fixtureReaders);
        $this->assertSame([['SomeModel1', 'SomeModel1_Extends'], ['SomeModel2', 'SomeModel2_extends']], array_map(function (FixtureReader $fixtureReader) {
            return $fixtureReader->getFabricateDefineNames();
        }, $fixtureReaders));
    }

    public function testIgnoreFunctions()
    {
        $app = new CakePhp2App($this->fixtureAppPath());
        $app->addModelDir($this->fixtureAppPath('AdditionalModel'));
        $app->addIgnoreFile($this->fixtureAppPath('Model/SomeModel1.php'));

        $modelReaders = $app->getModelReaders();

        $this->assertCount(3, $modelReaders);
        $this->assertSame(['AppModel', 'SomeModel2', 'SomeModel5'], array_map(function (ModelReader $modelReader) {
            return $modelReader->getModelName();
        }, $modelReaders));
    }
}
