<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\PhpParser\Visitors;

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class GetTargetVisitor extends NodeVisitorAbstract
{
    private $targetClassName;
    private $inTargetClass = false;
    private $targets = [];
    private $filter;

    public function __construct(string $targetClassName, callable $filter)
    {
        $this->targetClassName = $targetClassName;
        $this->filter = $filter;
    }

    /**
     * @return Node[]
     */
    public function getTargets(): array
    {
        return $this->targets;
    }

    public function getFirstTarget(): ?Node
    {
        return $this->targets[0] ?? null;
    }

    public function leaveNode(Node $node)
    {
        if ($this->isTargetClass($node)) {
            $this->inTargetClass = false;
        }

        return null;
    }

    public function enterNode(Node $node)
    {
        if ($this->isTargetClass($node)) {
            $this->inTargetClass = true;
        }

        $filter = $this->filter;
        if ($this->inTargetClass && $filter($node)) {
            $this->targets[] = $node;
            return NodeTraverser::DONT_TRAVERSE_CHILDREN;
        }

        return null;
    }

    private function isTargetClass(Node $node): bool
    {
        return $node instanceof Node\Stmt\ClassLike && $node->name->toString() === $this->targetClassName;
    }
}
