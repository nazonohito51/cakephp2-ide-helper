<?php
declare(strict_types=1);

namespace Tests\Unit\CakePhp2Analyzer\Readers;

use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ModelReader;
use Tests\TestCase;

class ModelReaderTest extends TestCase
{
    public function testGettersWhenModelInApp()
    {
        $modelReader = new ModelReader($this->fixtureAppPath('Model/SomeModel1.php'));

        $this->assertSame('SomeModel1', $modelReader->getModelName());
        $this->assertFalse($modelReader->isPlugin());
        $this->assertSame('', $modelReader->getPluginName());
        $this->assertSame('SomeModel1', $modelReader->getSymbol());
    }

    public function testGettersWhenModelInPlugin()
    {
        $modelReader = new ModelReader($this->fixtureAppPath('Model/SomeModel1.php'), 'SomePlugin');

        $this->assertSame('SomeModel1', $modelReader->getModelName());
        $this->assertTrue($modelReader->isPlugin());
        $this->assertSame('SomePlugin', $modelReader->getPluginName());
        $this->assertSame('SomePlugin.SomeModel1', $modelReader->getSymbol());
    }

    public function testGetBehaviorSymbols()
    {
        $modelReader = new ModelReader($this->fixtureAppPath('Model/SomeModel1.php'));

        $this->assertSame(['SomeBehavior1', 'SomeBehavior2', 'SomePlugin1.SomeBehavior3'], $modelReader->getBehaviorSymbols());
    }

    public function testGetPhpDoc()
    {
        $modelReader = new ModelReader($this->fixtureAppPath('Model/SomeModel1.php'));

        $expected = <<<PHPDOC
/**
 * This is summary.
 *
 * This is
 * description.
 *
 * @property \CakePhp2IdeHelper\Generator hoge
 * @mixin \CakePhp2IdeHelper\SomeBehavior1
 */
PHPDOC;
        $this->assertSame($expected, $modelReader->getPhpDoc());
    }
}
