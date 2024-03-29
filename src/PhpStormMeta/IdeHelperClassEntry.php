<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\PhpStormMeta;

use PhpParser\Builder\Class_;
use PhpParser\BuilderFactory;
use PhpParser\Comment\Doc;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;

class IdeHelperClassEntry
{
    private $className;
    private $parentClassName;
    private $classMethods = [];
    protected $isAbstract = false;

    public function __construct(string $className, string $parentClassName = null)
    {
        $this->className = $className;
        $this->parentClassName = $parentClassName;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getParentClassName(): ?string
    {
        return $this->parentClassName;
    }

    /**
     * @return ClassMethod[]
     */
    public function getMethods(): array
    {
        return $this->classMethods;
    }

    public function setAbstract(bool $abstract): void
    {
        $this->isAbstract = $abstract;
    }

    public function addMethod(ClassMethod $classMethod): void
    {
        if (count($classMethod->params) > 0) {
            $this->classMethods[] = clone $classMethod;
        }
    }

    public function createStmt(): Class_
    {
        $builderFactory = new BuilderFactory;
        $classStmt = $builderFactory->class($this->getClassName());
        if (!is_null($this->getParentClassName())) {
            $classStmt->extend($this->getParentClassName());
        }
        if ($this->isAbstract) {
            $classStmt->makeAbstract();
        }

        foreach ($this->getMethods() as $classMethod) {
            // remove first argument
            $firstArg = array_shift($classMethod->params);

            // remove method body
            $classMethod->stmts = [];

            $variable = $builderFactory->var('behavior');

            // create method call statement for call original behavior method
            $methodCall = $builderFactory->methodCall($variable, $classMethod->name->toString(), []);
            $methodCall->args[] = $firstArg->var;
            foreach ($classMethod->params as $param) {
                $methodCall->args[] = $param->var;
            }
            if ($classMethod->getReturnType() instanceof Identifier && $classMethod->getReturnType()->toLowerString() === 'void') {
                // void method must not return
                $newStatement = new Expression($methodCall);
            } else {
                $newStatement = new Return_($methodCall);
            }

            $newStatement->setDocComment(
                new Doc(
                    "/**
                        * @var \\{$this->getClassName()} \$behavior
                        * @var \\Model \${$firstArg->var->name}
                        */"
                )
            );
            $classMethod->stmts[] = $newStatement;

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
        foreach ($this->getMethods() as $classMethod) {
            $ret[] = "\\{$this->getClassName()}::{$classMethod->name->toString()}()";
        }

        return $ret;
    }
}
