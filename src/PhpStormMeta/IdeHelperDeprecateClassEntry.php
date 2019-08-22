<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\PhpStormMeta;

use PhpParser\Builder\Class_;
use PhpParser\BuilderFactory;
use PhpParser\Comment\Doc;

class IdeHelperDeprecateClassEntry extends IdeHelperClassEntry
{
    public function __construct(string $className, string $parentClassName = null)
    {
        if (!is_null($parentClassName)) {
            $parentClassName = 'Deprecate' . $parentClassName;
        }
        parent::__construct('Deprecate' . $className, $parentClassName);
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

            $classMethod->setAttribute('comments', []);
            $classMethod->setDocComment(new Doc("/**
            * @deprecated
            */"));

            $classStmt->addStmt($classMethod);
        }

        return $classStmt;
    }
}
