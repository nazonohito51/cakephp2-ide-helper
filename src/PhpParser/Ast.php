<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\PhpParser;

use CakePhp2IdeHelper\PhpParser\Visitors\GetTargetVisitor;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

class Ast
{
    private $file;

    /**
     * @var Stmt[]
     */
    private $statements;

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

    public function getClassLike(): Stmt\ClassLike
    {
        $visitor = new GetTargetVisitor($this->file->getBasename('.php'), static function (Node $node): bool {
            return $node instanceof Stmt\ClassLike;
        });

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($this->getStatements());

        return $visitor->getFirstTarget();
    }

    public function getProperty(string $propertyName): ?Stmt\PropertyProperty
    {
        $visitor = new GetTargetVisitor($this->file->getBasename('.php'), static function (Node $node) use ($propertyName): bool {
            return ($node instanceof Stmt\PropertyProperty && $node->name->toString() === $propertyName);
        });
//        $property = (new NodeFinder)->findFirst($this->statements, function(Node $node) use ($propertyName) {
//            return $node instanceof Node\Stmt\PropertyProperty && $node->name->toString() === $propertyName;
//        });

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($this->getStatements());

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

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($this->getStatements());

        return $visitor->getTargets();
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

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);
        $traverser->traverse($this->getStatements());

        return $visitor->getTargets();
    }

    public function fixComment()
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor($this->getVisitor());
        $newAst = $traverser->traverse($this->getStatements());

        return (new Standard)->prettyPrintFile($newAst);
    }

    public function getVisitor(): NodeVisitorAbstract
    {
        return new class($this->file->getBasename('.php')) extends NodeVisitorAbstract {
            private $targetClassName;

            public function __construct(string $targetClassName)
            {
                $this->targetClassName = $targetClassName;
            }

            public function leaveNode(Node $node)
            {
                if ($this->isTargetClass($node)) {
                    /** @var Stmt\ClassLike $node */
                    $docComment = $node->getDocComment();
                    $comments = explode("\n", $docComment->getText());
                    $lastLine = array_pop($comments);
                    $comments[] = '@property Hoge $hoge';
                    $comments[] = $lastLine;
                    $node->setDocComment(new Doc(implode("\n", $comments), $docComment->getLine(), $docComment->getFilePos(), $docComment->getTokenPos()));
                }

                return null;
            }

            public function enterNode(Node $node)
            {
                //
            }

            private function isTargetClass(Node $node)
            {
                return ($node instanceof Stmt\ClassLike && $node->name === $this->targetClassName);
            }
        };
    }
}
