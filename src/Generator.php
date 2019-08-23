<?php

namespace CakePhp2IdeHelper;

use Barryvdh\Reflection\DocBlock;
use Barryvdh\Reflection\DocBlock\Tag;
use CakePhp2IdeHelper\CakePhp2Analyzer\CakePhp2AppAnalyzer;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ModelReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\StructuralElements\CakePhp2App;
use CakePhp2IdeHelper\Exception\FailedUpdatingPhpDocException;
use CakePhp2IdeHelper\PhpStormMeta\ExpectArgumentsEntry;
use CakePhp2IdeHelper\PhpStormMeta\IdeHelperClassEntry;
use CakePhp2IdeHelper\PhpStormMeta\IdeHelperContent;
use CakePhp2IdeHelper\PhpStormMeta\IdeHelperDeprecateClassEntry;
use CakePhp2IdeHelper\PhpStormMeta\OverRideEntry;
use CakePhp2IdeHelper\PhpStormMeta\UpdateModelDocEntry;

class Generator
{
    private $rootDir;
    private $analyzer;

    public function __construct(string $rootDir, CakePhp2App $app)
    {
        $this->rootDir = $rootDir;
        $this->analyzer = new CakePhp2AppAnalyzer($app);
    }

    public function generatePhpStormMetaFileContent(): string
    {
        $overrideEntries = [$this->createClassRegistryReturnTypeOverride()];
        $expectArgumentsEntries = [
            $this->createClassRegistryExpectArgument(),
            $this->createFabricateExpectArgument(),
            $this->createModelFindFirstArgument(),
            $this->createModelFindSecondArgument(),
            $this->createSwitchableDataSourceWithReadArgument(),
            $this->createSwitchableDataSourceWithWriteArgument(),
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
            $entry->addAsString($modelReader->getSymbol());
        }

        return $entry;
    }

    private function createFabricateExpectArgument(): ExpectArgumentsEntry
    {
        $entry = new ExpectArgumentsEntry('\\Fabricate\\Fabricate::create()', 0);
        foreach ($this->analyzer->getFixtureReaders() as $fixtureReader) {
            foreach ($fixtureReader->getFabricateDefineNames() as $fabricateDefineName) {
                $entry->addAsString($fabricateDefineName);
            }
        }

        return $entry;
    }

    private function createModelFindFirstArgument(): ExpectArgumentsEntry
    {
        $entry = new ExpectArgumentsEntry('\\Model::find()', 0);
        foreach (['first', 'count', 'all', 'list', 'threaded', 'neighbors'] as $arg) {
            $entry->addAsString($arg);
        }

        return $entry;
    }

    private function createModelFindSecondArgument(): ExpectArgumentsEntry
    {
        $entry = new ExpectArgumentsEntry('\\Model::find()', 1);
        $entry->add('[
            "conditions" => null,
            "fields" => null,
            "joins" => [],
            "limit" => null,
            "offset" => null,
            "order" => null,
            "page" => 1,
            "group" => null,
            "callbacks" => true,
        ]');

        return $entry;
    }

    private function createSwitchableDataSourceWithReadArgument(): ExpectArgumentsEntry
    {
        $ideHelperNamespace = IdeHelperContent::NAMESPACE;
        $entry = new ExpectArgumentsEntry("\\{$ideHelperNamespace}\\SwitchableDatasourceBehavior::withRead()", 0);
        $entry->add("function (Model \$model) {\n            return;\n        }");
        $entry->add("static function (Model \$model) {\n            return;\n        }");

        return $entry;
    }

    private function createSwitchableDataSourceWithWriteArgument(): ExpectArgumentsEntry
    {
        $ideHelperNamespace = IdeHelperContent::NAMESPACE;
        $entry = new ExpectArgumentsEntry("\\{$ideHelperNamespace}\\SwitchableDatasourceBehavior::withWrite()", 0);
        $entry->add("function (Model \$model) {\n            return;\n        }");
        $entry->add("static function (Model \$model) {\n            return;\n        }");

        return $entry;
    }

    public function generateIdeHelperContent(): IdeHelperContent
    {
        $behaviorExtendsGraph = $this->analyzer->getBehaviorExtendsGraph();
        $content = new IdeHelperContent();
        foreach ($this->analyzer->getBehaviorReaders() as $behaviorReader) {
            if ($content->haveClassEntry($behaviorReader->getBehaviorName())) {
                // TODO: display error
                continue;
            }

            $parentBehavior = !is_null($behaviorExtendsGraph->getParent($behaviorReader)) ?
                $behaviorExtendsGraph->getParent($behaviorReader)->getBehaviorName() : null;
            $classEntry = new IdeHelperClassEntry($behaviorReader->getBehaviorName(), $parentBehavior);
            $classEntry->setAbstract($behaviorReader->isAbstractClass());
            $deprecateClassEntry = new IdeHelperDeprecateClassEntry($behaviorReader->getBehaviorName(), $parentBehavior);
            $deprecateClassEntry->setAbstract($behaviorReader->isAbstractClass());

            foreach ($behaviorReader->getPublicMethods() as $method) {
                if (!$method->isAbstract()) {
                    $classEntry->addMethod($method);
                    $deprecateClassEntry->addMethod($method);
                }
            }

            $content->addEntry($classEntry);
            $content->addEntry($deprecateClassEntry);
        }

        return $content;
    }

    /**
     * @return UpdateModelDocEntry[]
     */
    public function generateModelDocEntries(): array
    {
        $ideHelperContent = $this->generateIdeHelperContent();

        $ret = [];
        foreach ($this->analyzer->getModelReaders() as $modelReader) {
            if (!empty($this->analyzer->analyzeBehaviorsOf($modelReader))) {
                try {
                    $ret[] = $this->createModelDocEntry($modelReader, $ideHelperContent);
                } catch (FailedUpdatingPhpDocException $e) {
                    // TODO: error handling
                }
            }
        }

        return $ret;
    }

    public function createModelDocEntry(ModelReader $modelReader, IdeHelperContent $content): UpdateModelDocEntry
    {
        $entry = new UpdateModelDocEntry($modelReader);

        foreach ($modelReader->getBehaviorSymbols() as $behaviorSymbol) {
            if ($behaviorReader = $this->analyzer->searchBehaviorFromSymbol($behaviorSymbol)) {
                $mockClassName = $content->getMockClassFromOriginalClass($behaviorReader->getBehaviorName());
                $entry->appendTagWhenNotExist("@mixin {$mockClassName}");
            }
        }

        $parentBehaviors = $this->analyzer->getModelExtendsGraph()->getParentBehaviors($modelReader);
        $targetRealBehaviors = $this->analyzer->analyzeBehaviorsOf($modelReader);
        $expectedExtendsBehaviors = array_diff($targetRealBehaviors, $modelReader->getBehaviorSymbols());
        foreach (array_diff($parentBehaviors, $expectedExtendsBehaviors) as $shouldDeprecateBehaviorSymbol) {
            if (in_array($shouldDeprecateBehaviorSymbol, $modelReader->getBehaviorSymbols(), true)) {
                continue;
            }
            $behaviorReader = $this->analyzer->searchBehaviorFromSymbol($shouldDeprecateBehaviorSymbol);
            $mockClassName = $content->getDeprecateMockClassFromOriginalClass($behaviorReader->getBehaviorName());
            $entry->appendTagWhenNotExist("@mixin {$mockClassName}");
        }

        return $entry;
    }

    private function getMetaFileTemplatePath(): string
    {
        return __DIR__ . '/../resources/phpstorm.meta.template.php';
    }
}
