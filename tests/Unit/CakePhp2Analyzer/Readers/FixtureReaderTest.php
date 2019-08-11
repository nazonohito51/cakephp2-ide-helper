<?php
declare(strict_types=1);

namespace Tests\Unit\CakePhp2Analyzer\Readers;

use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\FixtureReader;
use Tests\TestCase;

class FixtureReaderTest extends TestCase
{
    public function testGetFabricateDefineNames()
    {
        $fixtureReader = new FixtureReader($this->fixtureAppPath('Test/Fixture/SomeFabricateFixture1.php'));

        $this->assertSame(['SomeModel1', 'SomeModel1_Extends'], $fixtureReader->getFabricateDefineNames());
    }
}
