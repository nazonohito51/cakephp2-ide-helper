<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\PhpStormMeta;

use PhpParser\Builder\Class_;
use PhpParser\BuilderFactory;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinter\Standard;

class IdeHelperDeprecateClassEntry extends IdeHelperClassEntry
{
    public function __construct(string $className)
    {
        parent::__construct('Deprecate' . $className);
    }

    public function createStmt(): Class_
    {
        $builderFactory = new BuilderFactory;
        $classStmt = $builderFactory->class($this->getClassName());

        foreach ($this->getMethods() as $classMethod) {
            // remove first argument
            $firstArg = array_shift($classMethod->params);

            // remove method body
            $classMethod->stmts = [];

            $classMethod->setAttribute('comments', []);
            $classMethod->setDocComment(new Doc("/**
            * @deprecated
            */"));

            $classStmt->addStmt($classMethod);
        }

        return $classStmt;
    }
}
