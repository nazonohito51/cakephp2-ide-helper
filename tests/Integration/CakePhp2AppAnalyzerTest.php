<?php
declare(strict_types=1);

namespace Tests\Integration;

use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\BehaviorReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\StructualElements\CakePhp2App;
use CakePhp2IdeHelper\CakePhp2Analyzer\CakePhp2AppAnalyzer;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ModelReader;
use Tests\TestCase;

class CakePhp2AppAnalyzerTest extends TestCase
{
    public function testGetModelReaders()
    {
        $app = new CakePhp2App($this->fixturePath('cake2AppSample/app'));
        $app->addModelDir($this->fixturePath('cake2AppSample/app/AdditionalModel'));
        $analyzer = new CakePhp2AppAnalyzer($app);

        $modelReaders = $analyzer->getModelReaders();

        $this->assertCount(6, $modelReaders);
        $this->assertSame(['AppModel', 'SomeModel1', 'SomeModel2', 'SomeModel5', 'SomePlugin1.SomeModel3', 'SomePlugin1.SomeModel4'], array_map(function (ModelReader $reader) {
            return $reader->getSymbol();
        }, $modelReaders));
    }

    public function testGetBehaviorReaders()
    {
        $app = new CakePhp2App($this->fixturePath('cake2AppSample/app'));
        $app->addModelDir($this->fixturePath('cake2AppSample/app/AdditionalModel'));
        $analyzer = new CakePhp2AppAnalyzer($app);

        $behaviorReaders = $analyzer->getBehaviorReaders();

        $this->assertCount(3, $behaviorReaders);
        $this->assertSame(['SomeBehavior1', 'SomeBehavior2', 'SomePlugin1.SomeBehavior3'], array_map(function (BehaviorReader $reader) {
            return $reader->getSymbol();
        }, $behaviorReaders));
    }
}
