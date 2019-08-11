<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\PhpStormMeta;

use PhpParser\Builder\Class_;
use PhpParser\BuilderFactory;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\PrettyPrinter\Standard;

class IdeHelperClassEntry
{
    private $className;
    private $prettyPrinter;

    /**
     * @var ClassMethod[]
     */
    private $classMethods = [];

    public function __construct(string $className)
    {
        $this->className = $className;
        $this->prettyPrinter = new Standard();
    }

    public function addMethod(ClassMethod $classMethod): void
    {
        $this->classMethods[] = $classMethod;
    }

    public function createStmt(): Class_
    {
        return (new BuilderFactory)->class($this->className)->makeAbstract()->addStmts($this->classMethods);
    }
}
