<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\CakePhp2Analyzer;

use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ModelReader;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;

class ModelExtendsGraph
{
    private $graph;

    public function __construct()
    {
        $this->graph = new Graph();
    }

    public function addExtends(ModelReader $child, ModelReader $parent): void
    {
        $childVertex = $this->getVertex($child);
        $parentVertex = $this->getVertex($parent);
        if (count($edges = $childVertex->getEdgesTo($parentVertex)) === 0) {
            $childVertex->createEdgeTo($parentVertex);
        }
    }

    private function getVertex(ModelReader $modelReader): Vertex
    {
        if ($this->graph->hasVertex($modelReader->getModelName())) {
            $vertex = $this->graph->getVertex($modelReader->getModelName());
            $readers = $vertex->getAttribute('readers');

            $exist = false;
            foreach ($readers as $reader) {
                /** @var ModelReader $reader */
                if ($modelReader->getRealPath() === $reader->getRealPath()) {
                    // duplicated
                    $exist = true;
                }
            }

            if (!$exist) {
                $readers[] = $modelReader;
                $vertex->setAttribute('readers', $readers);
            }
            return $vertex;
        }

        $vertex = $this->graph->createVertex($modelReader->getModelName());
        $vertex->setAttribute('readers', [$modelReader]);
        return $vertex;
    }

    public function getParentBehaviors(ModelReader $modelReader): array
    {
        $parentBehaviors = [];
        foreach ($this->getParents($modelReader) as $parent) {
            foreach ($parent->getBehaviorSymbols() as $behaviorSymbol) {
                if (!in_array($behaviorSymbol, $parentBehaviors, true)) {
                    $parentBehaviors[] = $behaviorSymbol;
                }
            }
        }

        return $parentBehaviors;
    }

    /**
     * @param ModelReader $modelReader
     * @return ModelReader[]
     */
    public function getParents(ModelReader $modelReader): array
    {
        $ret = [];
        $child = $modelReader;
        while (!is_null($parent = $this->getParent($child))) {
            $ret[] = $parent;
            $child = $parent;
        }

        return $ret;
    }

    public function getParent(ModelReader $modelReader): ?ModelReader
    {
        $child = $this->getVertex($modelReader);

        if (count($parents = $child->getVerticesEdgeTo()) > 1) {
            throw new \LogicException("{$modelReader->getModelName()} have multiple parents");
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
