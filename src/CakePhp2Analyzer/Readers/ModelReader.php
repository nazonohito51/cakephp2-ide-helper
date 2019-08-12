<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\CakePhp2Analyzer\Readers;

use CakePhp2IdeHelper\PhpParser\Ast;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar\String_;

class ModelReader
{
    private $file;
    private $pluginName;
    private $ast;

    public function __construct(string $path, string $pluginName = '')
    {
        if (!is_file($path)) {
            throw new \InvalidArgumentException('invalid model path: ' . $path);
        }

        $this->file = new \SplFileObject($path);
        $this->pluginName = $pluginName;
        $this->ast = new Ast($path);
    }

    public function getModelName(): string
    {
        return $this->file->getBasename('.php');
    }

    public function getRealPath(): string
    {
        return $this->file->getRealPath();
    }

    public function getContent(): string
    {
        return file_get_contents($this->getRealPath());
    }

    public function isPlugin(): bool
    {
        return !empty($this->pluginName);
    }

    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    public function getSymbol(): string
    {
        if ($this->isPlugin()) {
            return "{$this->getPluginName()}.{$this->getModelName()}";
        }

        return $this->getModelName();
    }

    /**
     * @return string[]
     */
    public function getBehaviorSymbols(): array
    {
        if (is_null($property = $this->ast->getProperty('actsAs'))) {
            return [];
        }

        $behaviorSymbols = [];
        if (!is_null($property->default) && $property->default instanceof Array_) {
            foreach ($property->default->items as $item) {
                if ($item instanceof ArrayItem) {
                    if (!is_null($item->key) && $item->key instanceof String_) {
                        $behaviorSymbols[] = $item->key->value;
                    } elseif ($item->value instanceof String_) {
                        $behaviorSymbols[] = $item->value->value;
                    }
                }
            }
        }

        return $behaviorSymbols;
    }

    public function havePhpDoc(): bool
    {
        return !is_null($this->getPhpDoc());
    }

    public function getPhpDoc(): ?string
    {
        $classLike = $this->ast->getClassLike();
        $docComment = $classLike->getDocComment();
        return !is_null($docComment) ? $docComment->__toString() : null;
    }
}
