<?php
declare(strict_types=1);

namespace Tests\Unit\CakePhp2Analyzer\Readers;

use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\BehaviorReader;
use PhpParser\Node\Stmt\ClassMethod;
use Tests\TestCase;

class BehaviorReaderTest extends TestCase
{
    public function testGetPublicMethods()
    {
        $behaviorReader = new BehaviorReader($this->fixtureAppPath('Model/Behavior/SomeBehavior1.php'));

        $publicMethods = $behaviorReader->getPublicMethods();

        $this->assertSame(['someBehavior1Method1', 'someBehavior1Method2', 'someBehavior1Method3'], array_map(function (ClassMethod $classMethod) {
            return $classMethod->name->toString();
        }, $publicMethods));
    }
}
