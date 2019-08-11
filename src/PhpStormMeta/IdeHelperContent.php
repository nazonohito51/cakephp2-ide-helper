<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\PhpStormMeta;

use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\PrettyPrinter\Standard;

class IdeHelperContent
{
    /**
     * @var IdeHelperClassEntry[]
     */
    private $entries = [];

    public function addEntry(IdeHelperClassEntry $entry)
    {
        $this->entries[] = $entry;
    }

    public function __toString(): string
    {
        $factory = (new BuilderFactory)->namespace('CakePhp2IdeHelper');

        foreach ($this->entries as $entry) {
            $factory->addStmt($entry->createStmt());
        }

        return (new Standard())->prettyPrintFile([$factory->getNode()]);
    }
}
