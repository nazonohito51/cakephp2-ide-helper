<?php

namespace CakePhp2IdeHelper;

use CakePhp2IdeHelper\CakePhp2Analyzer\CakePhp2AppAnalyzer;
use CakePhp2IdeHelper\CakePhp2Analyzer\StructuralElements\CakePhp2App;
use CakePhp2IdeHelper\PhpStormMeta\ExpectArgumentsEntry;
use CakePhp2IdeHelper\PhpStormMeta\IdeHelperClassEntry;
use CakePhp2IdeHelper\PhpStormMeta\IdeHelperContent;
use CakePhp2IdeHelper\PhpStormMeta\OverRideEntry;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\Class_;

class Generator
{
    private $rootDir;
    private $analyzer;

    public function __construct(string $rootDir, string $appDir)
    {
        $this->rootDir = $rootDir;
        $this->analyzer = new CakePhp2AppAnalyzer(new CakePhp2App($appDir));
    }

    public function generate(): void
    {
        $phpstormMetaFile = new \SplFileObject($this->rootDir . '/.phpstorm.meta.php', 'w');
        $phpstormMetaFile->fwrite($this->generatePhpStormMetaFileContent());

        $ideHelperFile = new \SplFileObject($this->rootDir . '/_ide_helper.php', 'w');
        $ideHelperFile->fwrite($this->createIdeHelperContent());
    }

    public function generatePhpStormMetaFileContent(): string
    {
        $overrideEntries = [$this->createClassRegistryReturnTypeOverride()];
        $expectArgumentsEntries = [
            $this->createClassRegistryExpectArgument(),
            $this->createFabricateExpectArgument()
        ];

        // TODO: create getDataSource return type

        $content = '<?php' . "\n";

        ob_start();
        include $this->getMetaFileTemplatePath();
        $content .= ob_get_contents();
        ob_end_clean();

        return $content;
    }

    private function createClassRegistryReturnTypeOverride(): OverRideEntry
    {
        $entry = new OverRideEntry('\\ClassRegistry::init(0)');
        foreach ($this->analyzer->getModelReaders() as $modelReader) {
            $entry->add($modelReader->getSymbol(), $modelReader->getModelName());
        }

        return $entry;
    }

    private function createClassRegistryExpectArgument(): ExpectArgumentsEntry
    {
        $entry = new ExpectArgumentsEntry('\\ClassRegistry::init()', 0);
        foreach ($this->analyzer->getModelReaders() as $modelReader) {
            $entry->add($modelReader->getSymbol());
        }

        return $entry;
    }

    private function createFabricateExpectArgument(): ExpectArgumentsEntry
    {
        $entry = new ExpectArgumentsEntry('\\Fabricate\\Fabricate::create()', 0);
        foreach ($this->analyzer->getFixtureReaders() as $fixtureReader) {
            foreach ($fixtureReader->getFabricateDefineNames() as $fabricateDefineName) {
                $entry->add($fabricateDefineName);
            }
        }

        return $entry;
    }

    private function createIdeHelperContent(): IdeHelperContent
    {
        $content = new IdeHelperContent();
        foreach ($this->analyzer->getBehaviorReaders() as $behaviorReader) {
            $entry = new IdeHelperClassEntry($behaviorReader->getBehaviorName());
            foreach ($behaviorReader->getPublicMethods() as $method) {
                $method = clone $method;

                // remove first argument
                array_shift($method->params);
                // remove method body
                $method->stmts = null;
                // create phpdoc
                $fqsen = "\\{$behaviorReader->getBehaviorName()}::{$method->name->toString()}()";
                $method->setDocComment(new Doc("/**\n * @see {$fqsen}\n */"));
                // change to abstract function
                $method->flags |= Class_::MODIFIER_ABSTRACT;

                $entry->addMethod($method);
            }
            $content->addEntry($entry);
        }

        return $content;
    }

    public function updateModelPhpDoc()
    {
        $behaviorReaders = $this->analyzer->getBehaviorReaders();

        foreach ($this->analyzer->getModelReaders() as $modelReader) {
            // TODO: collect actsAs
            foreach ($modelReader->getBehaviorSymbols() as $behaviorSymbol) {
                if (!is_null($behaviorReader = $this->analyzer->searchBehaviorFromSymbol($behaviorSymbol))) {
                    // TODO: collect public methods in Behavior
                    // TODO: create _ide_helper.php(with analyze Behavior, and create that mocks)
                }
            }
            // TODO: update model phpdoc
        }
    }

    private function getMetaFileTemplatePath(): string
    {
        return __DIR__ . '/../resources/phpstorm.meta.template.php';
    }
}
