<?php

namespace CakePhp2IdeHelper;

use Barryvdh\Reflection\DocBlock;
use Barryvdh\Reflection\DocBlock\Serializer as DocBlockSerializer;
use Barryvdh\Reflection\DocBlock\Tag;
use CakePhp2IdeHelper\CakePhp2Analyzer\CakePhp2AppAnalyzer;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ModelReader;
use CakePhp2IdeHelper\CakePhp2Analyzer\StructuralElements\CakePhp2App;
use CakePhp2IdeHelper\Exception\FailedUpdatingPhpDocException;
use CakePhp2IdeHelper\PhpStormMeta\ExpectArgumentsEntry;
use CakePhp2IdeHelper\PhpStormMeta\IdeHelperClassEntry;
use CakePhp2IdeHelper\PhpStormMeta\IdeHelperContent;
use CakePhp2IdeHelper\PhpStormMeta\OverRideEntry;
use CakePhp2IdeHelper\PhpStormMeta\UpdateModelDocEntry;
use PhpParser\Comment\Doc;

class Generator
{
    private $rootDir;
    private $analyzer;

    public function __construct(string $rootDir, CakePhp2App $app)
    {
        $this->rootDir = $rootDir;
        $this->analyzer = new CakePhp2AppAnalyzer($app);
    }

    public function generate(): void
    {
        $phpstormMetaFile = new \SplFileObject($this->rootDir . '/.phpstorm.meta.php', 'w');
        $phpstormMetaFile->fwrite($this->generatePhpStormMetaFileContent());

        $ideHelperContent = $this->generateIdeHelperContent();
        $ideHelperFile = new \SplFileObject($this->rootDir . '/_ide_helper.php', 'w');
        $ideHelperFile->fwrite($ideHelperContent);

        foreach ($this->analyzer->getModelReaders() as $modelReader) {
            if (!empty($modelReader->getBehaviorSymbols())) {
                try {
                    $this->createModelDocEntry($modelReader, $ideHelperContent);
                } catch (FailedUpdatingPhpDocException $e) {
                    // TODO: error handling
                }
            }
        }
    }

    public function generatePhpStormMetaFileContent(): string
    {
        $overrideEntries = [$this->createClassRegistryReturnTypeOverride()];
        $expectArgumentsEntries = [
            $this->createClassRegistryExpectArgument(),
            $this->createFabricateExpectArgument(),
            $this->createModelMethodArgument()
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

    private function createModelMethodArgument(): ExpectArgumentsEntry
    {
        $entry = new ExpectArgumentsEntry('\\Model::find()', 0);
        foreach (['first', 'count', 'all', 'list', 'threaded', 'neighbors'] as $arg) {
            $entry->add($arg);
        }

        return $entry;
    }

    public function generateIdeHelperContent(): IdeHelperContent
    {
        $content = new IdeHelperContent();
        foreach ($this->analyzer->getBehaviorReaders() as $behaviorReader) {
            $classEntry = new IdeHelperClassEntry($behaviorReader->getBehaviorName());
            foreach ($behaviorReader->getPublicMethods() as $method) {
                // remove first argument
                array_shift($method->params);
                // remove method body
                $method->stmts = [];
                // create phpdoc
                $fqsen = "\\{$behaviorReader->getBehaviorName()}::{$method->name->toString()}()";
                $method->setDocComment(new Doc("/**\n * @see {$fqsen}\n */"));

                $classEntry->addMethod($method);
            }
            $content->addEntry($classEntry);
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
            if (!empty($modelReader->getBehaviorSymbols())) {
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
        $originalDocComment = $modelReader->getPhpDoc() ?? '';
        $replaceDoc = new DocBlock($originalDocComment);

        foreach ($modelReader->getBehaviorSymbols() as $behaviorSymbol) {
            if ($behaviorReader = $this->analyzer->searchBehaviorFromSymbol($behaviorSymbol)) {
                $className = $content->getMockClassFromOriginalClass($behaviorReader->getBehaviorName());
                $tag = Tag::createInstance("@mixin {$className} Added by cakephp2-ide-helper", $replaceDoc);

                $exist = false;
                foreach ($replaceDoc->getTags() as $existTag) {
                    if ($existTag->__toString() === $tag->__toString()) {
                        $exist = true;
                        break;
                    }
                }
                if (!$exist) {
                    $replaceDoc->appendTag($tag);
                }
            }
        }

        return new UpdateModelDocEntry($modelReader, $replaceDoc);

//        $replaceDocComment = (new DocBlockSerializer())->getDocComment($replaceDoc);
//        if (empty($replaceDoc->getShortDescription()) && empty($replaceDoc->getLongDescription()->getContents())) {
//            // remove summary, description
//            $replaceDocComment = str_replace("/**\n * \n * ", '/**', $replaceDocComment);
//        }

//        $file = new \SplFileObject($modelReader->getRealPath(), 'w');
//        $originalContent = $modelReader->getContent();
//        if (!empty($originalDocComment)) {
//            $replacedContents = str_replace($originalDocComment, $replaceDocComment, $originalContent);
//        } else {
//            $needle = "class {$modelReader->getModelName()}";
//            $replace = "{$replaceDocComment}\nclass {$modelReader->getModelName()}";
//            $pos = strpos($originalContent, $needle);
//            if ($pos === false) {
//                throw new FailedUpdatingPhpDocException($file->getRealPath());
//            }
//            $replacedContents = substr_replace($originalContent, $replace, $pos, strlen($needle));
//        }
//
//        $file->fwrite($replacedContents);
    }

    private function getMetaFileTemplatePath(): string
    {
        return __DIR__ . '/../resources/phpstorm.meta.template.php';
    }
}
