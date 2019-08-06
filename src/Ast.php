<?php

namespace CakePhp2IdeHelper;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

class Ast
{
    private $path;
    private $stmts;

    public function __construct($path)
    {
        $this->path = $path;
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->stmts = $parser->parse(file_get_contents($path));
    }

    public function fixComment()
    {
        $traverser = new NodeTraverser();
        $traverser->addVisitor($this->getVisitor());
        $newAst = $traverser->traverse($this->stmts);

        return (new Standard)->prettyPrintFile($newAst);
    }

    public function getVisitor()
    {
        return new class extends NodeVisitorAbstract {
            private $targetClassName;

            public function __construct(string $targetClassName)
            {
                $this->targetClassName = $targetClassName;
            }

            public function leaveNode(Node $node) {
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

            public function enterNode(Node $node) {
                //
            }

            private function isTargetClass(Node $node)
            {
                return ($node instanceof Stmt\ClassLike && $node->name === $this->targetClassName);
            }
        };
    }
}
