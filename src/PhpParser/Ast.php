<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\PhpParser;

use CakePhp2IdeHelper\PhpParser\Visitors\GetTargetVisitor;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

class Ast
{
    private $file;

    /**
     * @var Stmt[]
     */
    private $statements;

    private $classLike;

    public function __construct(string $path)
    {
        $this->file = new \SplFileInfo($path);
    }

    private function getStatements(): array
    {
        if (is_null($this->statements)) {
            $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
            $this->statements = $parser->parse(file_get_contents($this->file->getRealPath()));
        }

        return $this->statements;
    }

    private function traverse(NodeVisitorAbstract $visitor): void
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($this->getStatements());
    }

    public function getClassLike(): Stmt\ClassLike
    {
        if (!is_null($this->classLike)) {
            return $this->classLike;
        }

        $visitor = new GetTargetVisitor($this->file->getBasename('.php'), static function (Node $node): bool {
            return $node instanceof Stmt\ClassLike;
        });

        $this->traverse($visitor);

        return $this->classLike = $visitor->getFirstTarget();
    }

    public function getProperty(string $propertyName): ?Stmt\PropertyProperty
    {
        $visitor = new GetTargetVisitor($this->file->getBasename('.php'), static function (Node $node) use ($propertyName): bool {
            return ($node instanceof Stmt\PropertyProperty && $node->name->toString() === $propertyName);
        });

        $this->traverse($visitor);

        return $visitor->getFirstTarget();
    }

    /**
     * @return Stmt\ClassMethod[]
     */
    public function getPublicMethods(): array
    {
        $visitor = new GetTargetVisitor($this->file->getBasename('.php'), static function (Node $node): bool {
            return $node instanceof Stmt\ClassMethod && $node->isPublic();
        });

        $this->traverse($visitor);

        return $visitor->getTargets();
    }

    /**
     * @param string $methodName
     * @return Node\Expr\MethodCall[]
     */
    public function getMethodCalls(string $methodName): array
    {
        $visitor = new class ($methodName) extends NodeVisitorAbstract {
            private $methodName;
            private $methodCalls = [];

            public function __construct(string $methodName)
            {
                $this->methodName = strtolower($methodName);
            }

            public function enterNode(Node $node)
            {
                if ($node instanceof Node\Expr\MethodCall) {
                    if ($node->name->toLowerString() === $this->methodName) {
                        $this->methodCalls[] = $node;
                    }
                }
                return null;
            }

            public function getMethodCalls(): array
            {
                return $this->methodCalls;
            }
        };

        $this->traverse($visitor);

        return $visitor->getMethodCalls();
    }

    /**
     * @param string $methodName
     * @return Node\Expr\StaticCall[]
     */
    public function getStaticCalls(array $className, string $methodName): array
    {
        $visitor = new GetTargetVisitor($this->file->getBasename('.php'), static function (Node $node) use ($className, $methodName): bool {
            if ($node instanceof Node\Expr\StaticCall) {
                if ($node->name instanceof Node\Identifier && $node->name->toString() === $methodName) {
                    if ($node->class instanceof Node\Name && in_array($node->class->toString(), $className, true)) {
                        return true;
                    }
                }
            }

            return false;
        });

        $this->traverse($visitor);

        return $visitor->getTargets();
    }
}
