<?php
declare(strict_types=1);

namespace Test\Unit\CakePhp2IdeHelper;

use CakePhp2IdeHelper\CakePhp2Analyzer\CakePhp2AppAnalyzer;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\BehaviorReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\FixtureReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ModelReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\StructuralElements\CakePhp2App;
use Tests\TestCase;

class CakePhp2AppAnalyzerTest extends TestCase
{
    public function testGetModelReaders()
    {
        $analyzer = new CakePhp2AppAnalyzer(new CakePhp2App($this->fixtureAppPath()));

        $modelReaders = $analyzer->getModelReaders();

        $this->assertCount(5, $modelReaders);
        $this->assertSame(['AppModel', 'SomeModel1', 'SomeModel2', 'SomeModel3', 'SomeModel4'], array_map(function (ModelReader $reader) {
            return $reader->getModelName();
        }, $modelReaders));
    }

    public function testGetBehaviorReaders()
    {
        $analyzer = new CakePhp2AppAnalyzer(new CakePhp2App($this->fixtureAppPath()));

        $behaviorReaders = $analyzer->getBehaviorReaders();

        $this->assertCount(3, $behaviorReaders);
        $this->assertSame(['SomeBehavior1', 'SomeBehavior2', 'SomeBehavior3'], array_map(function (BehaviorReader $reader) {
            return $reader->getBehaviorName();
        }, $behaviorReaders));
    }

    public function testGetFixtureReaders()
    {
        $analyzer = new CakePhp2AppAnalyzer(new CakePhp2App($this->fixtureAppPath()));

        $fixtureReaders = $analyzer->getFixtureReaders();

        $this->assertCount(2, $fixtureReaders);
        $this->assertSame([['SomeModel1', 'SomeModel1_Extends'], ['SomeModel2', 'SomeModel2_extends']], array_map(function (FixtureReader $reader) {
            return $reader->getFabricateDefineNames();
        }, $fixtureReaders));
    }
}
