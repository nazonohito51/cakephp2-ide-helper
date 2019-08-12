<?php
declare(strict_types=1);

namespace Tests\Unit\CakePhp2Analyzer\Readers;

use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\BehaviorReader;
use PhpParser\Node\Stmt\ClassMethod;
use Tests\TestCase;

class BehaviorReaderTest extends TestCase
{
    public function testGetters()
    {
        $behaviorReader = new BehaviorReader($this->fixtureAppPath('Plugin/SomePlugin1/Model/Behavior/SomeBehavior3.php'), 'SomePlugin1');

        $this->assertSame('SomeBehavior3', $behaviorReader->getBehaviorName());
        $this->assertTrue($behaviorReader->isPlugin());
        $this->assertSame('SomePlugin1', $behaviorReader->getPluginName());
        $this->assertSame('SomePlugin1.SomeBehavior3', $behaviorReader->getSymbol());
    }

    public function testGetPublicMethods()
    {
        $behaviorReader = new BehaviorReader($this->fixtureAppPath('Model/Behavior/SomeBehavior1.php'));

        $publicMethods = $behaviorReader->getPublicMethods();

        $this->assertSame(['someBehavior1Method1', 'someBehavior1Method2', 'someBehavior1Method3'], array_map(function (ClassMethod $classMethod) {
            return $classMethod->name->toString();
        }, $publicMethods));
    }
}
