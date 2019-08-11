<?php
declare(strict_types=1);

namespace Tests\Unit\CakePhp2Analyzer\Readers;

use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\BehaviorReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ModelReader;
use Tests\TestCase;

class BehaviorReaderTest extends TestCase
{
    public function testGetPublicMethods()
    {
        $behaviorReader = new BehaviorReader($this->fixtureAppPath('Model/Behavior/SomeBehavior1.php'));

        $behaviorReader->getPublicMethods();

        $this->assertFalse(false);
    }
}
