<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\CakePhp2Analyzer;

use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\BehaviorReader;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;

class BehaviorExtendsGraph
{
    private $graph;

    public function __construct()
    {
        $this->graph = new Graph();
    }

    public function addExtends(BehaviorReader $child, BehaviorReader $parent): void
    {
        $childVertex = $this->getVertex($child);
        $parentVertex = $this->getVertex($parent);
        if (count($edges = $childVertex->getEdgesTo($parentVertex)) === 0) {
            $childVertex->createEdgeTo($parentVertex);
        }
    }

    private function getVertex(BehaviorReader $behaviorReader): Vertex
    {
        if ($this->graph->hasVertex($behaviorReader->getBehaviorName())) {
            $vertex = $this->graph->getVertex($behaviorReader->getBehaviorName());
            $readers = $vertex->getAttribute('readers');

            $exist = false;
            foreach ($readers as $reader) {
                /** @var BehaviorReader $reader */
                if ($behaviorReader->getRealPath() === $reader->getRealPath()) {
                    // duplicated
                    $exist = true;
                }
            }

            if (!$exist) {
                $readers[] = $behaviorReader;
                $vertex->setAttribute('readers', $readers);
            }
            return $vertex;
        }

        $vertex = $this->graph->createVertex($behaviorReader->getBehaviorName());
        $vertex->setAttribute('readers', [$behaviorReader]);
        return $vertex;
    }

    public function getParentBehaviors(BehaviorReader $behaviorReader): array
    {
        $parentBehaviors = [];
        foreach ($this->getParents($behaviorReader) as $parent) {
            foreach ($parent->getBehaviorSymbols() as $behaviorSymbol) {
                if (!in_array($behaviorSymbol, $parentBehaviors, true)) {
                    $parentBehaviors[] = $behaviorSymbol;
                }
            }
        }

        return $parentBehaviors;
    }

    /**
     * @param BehaviorReader $behaviorReader
     * @return BehaviorReader[]
     */
    public function getParents(BehaviorReader $behaviorReader): array
    {
        $ret = [];
        $child = $behaviorReader;
        while (!is_null($parent = $this->getParent($child))) {
            $ret[] = $parent;
            $child = $parent;
        }

        return $ret;
    }

    public function getParent(BehaviorReader $behaviorReader): ?BehaviorReader
    {
        $child = $this->getVertex($behaviorReader);

        if (count($parents = $child->getVerticesEdgeTo()) > 1) {
            throw new \LogicException("{$behaviorReader->getBehaviorName()} have multiple parents");
        } elseif (count($parents) === 1) {
            $vertex = $parents->getVertexFirst();
            $readers = $vertex->getAttribute('readers');
            if (count($readers) > 1) {
                throw new \LogicException('');
            }

            return $readers[0];
        }

        return null;
    }
}
