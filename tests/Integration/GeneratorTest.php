<?php
declare(strict_types=1);

namespace Tests\Integration;

use CakePhp2IdeHelper\CakePhp2Analyzer\StructuralElements\CakePhp2App;
use CakePhp2IdeHelper\Generator;
use Tests\TestCase;

class GeneratorTest extends TestCase
{
    public function testGeneratePhpStormMetaFileContent()
    {
        $app = new CakePhp2App($this->fixtureAppPath());
        $generator = new Generator(__DIR__ . '/../tmp/', $app);
        $ret = $generator->generatePhpStormMetaFileContent();

        $this->assertSame(file_get_contents($this->fixturePath('expected/.phpstorm.meta.php')), $ret);
    }

    public function testGenerateIdeHelperContent()
    {
        $app = new CakePhp2App($this->fixtureAppPath());
        $generator = new Generator(__DIR__ . '/../tmp/', $app);
        $ret = $generator->generateIdeHelperContent();

        $this->assertSame(file_get_contents($this->fixturePath('expected/_ide_helper.php')), $ret->__toString());
    }

    public function testGenerateModelDocEntries()
    {
        $app = new CakePhp2App($this->fixtureAppPath());
        $generator = new Generator(__DIR__ . '/../tmp/', $app);
        $ret = $generator->generateModelDocEntries();

        $this->assertCount(5, $ret);
        $this->assertSame(file_get_contents($this->fixturePath('expected/AppModel.php')), $ret[0]->getReplaceModelContent());
        $this->assertSame(file_get_contents($this->fixturePath('expected/SomeModel1.php')), $ret[1]->getReplaceModelContent());
        $this->assertSame(file_get_contents($this->fixturePath('expected/SomeModel2.php')), $ret[2]->getReplaceModelContent());
        $this->assertSame(file_get_contents($this->fixturePath('expected/SomeModel3.php')), $ret[3]->getReplaceModelContent());
        $this->assertSame(file_get_contents($this->fixturePath('expected/SomeModel4.php')), $ret[4]->getReplaceModelContent());
    }
}
