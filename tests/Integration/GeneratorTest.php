<?php
declare(strict_types=1);

namespace Tests\Integration;

use CakePhp2IdeHelper\Generator;
use Tests\TestCase;

class GeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $generator = new Generator(__DIR__ . '/../tmp/', $this->fixtureAppPath());
        $generator->generate();

        $this->assertTrue(true);
    }
}
