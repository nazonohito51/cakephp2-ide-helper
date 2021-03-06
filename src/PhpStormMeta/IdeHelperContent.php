<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\PhpStormMeta;

use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter\Standard;

class IdeHelperContent
{
    public const NAMESPACE = 'CakePhp2IdeHelper';

    /**
     * @var IdeHelperClassEntry[]
     */
    private $entries = [];

    public function addEntry(IdeHelperClassEntry $entry)
    {
        $this->entries[] = $entry;
    }

    public function haveClassEntry(string $className): bool
    {
        foreach ($this->entries as $entry) {
            if ($entry->getClassName() === $className) {
                return true;
            }
        }

        return false;
    }

    public function getMockClassFromOriginalClass(string $className): ?string
    {
        foreach ($this->entries as $entry) {
            if ($entry->getClassName() === $className) {
                $namespace = self::NAMESPACE;
                return "\\{$namespace}\\$className";
            }
        }

        return null;
    }

    public function getDeprecateMockClassFromOriginalClass(string $className): ?string
    {
        $className = 'Deprecate' . $className;
        foreach ($this->entries as $entry) {
            if ($entry->getClassName() === $className) {
                $namespace = self::NAMESPACE;
                return "\\{$namespace}\\$className";
            }
        }

        return null;
    }

    public function __toString(): string
    {
        $factory = (new BuilderFactory)->namespace(self::NAMESPACE)->setDocComment(
            '// phpcs:ignoreFile'
        );

        foreach ($this->entries as $entry) {
            $factory->addStmt($entry->createStmt());
        }

        return (new Standard())->prettyPrintFile([$factory->getNode()]) . "\n";
    }
}
