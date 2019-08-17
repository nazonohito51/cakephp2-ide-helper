<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\PhpStormMeta;

use PhpParser\Builder\Class_;
use PhpParser\BuilderFactory;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
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

    public function getClassName(): string
    {
        return $this->className;
    }

    public function addMethod(ClassMethod $classMethod): void
    {
        $this->classMethods[] = clone $classMethod;
    }

    public function createStmt(): Class_
    {
        $builderFactory = new BuilderFactory;
        $classStmt = $builderFactory->class($this->className);

        foreach ($this->classMethods as $classMethod) {
            $variable = $builderFactory->var('behavior');
            $methodCall = $builderFactory->methodCall($variable, $classMethod->name->toString(), []);
            foreach ($classMethod->params as $param) {
                $methodCall->args[] = $param->var;
            }
            $returnStmt = new Return_($methodCall);
            $returnStmt->setDocComment(new Doc("/**
            * @var \\{$this->getClassName()} \$behavior
            * @var \\Model \$model
            */"));

            // remove first argument
            $firstArg = array_shift($classMethod->params);
            // remove method body
            $classMethod->stmts = [];

            $classMethod->stmts[] = $returnStmt;

            $classStmt->addStmt($classMethod);
        }

        return $classStmt;
    }

    /**
     * @return string[]
     */
    public function getSymbols(): array
    {
        $ret = [];
        foreach ($this->classMethods as $classMethod) {
            $ret[] = "\\{$this->className}::{$classMethod->name->toString()}()";
        }

        return $ret;
    }
}
