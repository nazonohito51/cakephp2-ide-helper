<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\CakePhp2Analyzer\Readers;

use CakePhp2IdeHelper\PhpParser\Ast;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;

class ControllerReader extends PhpFileReader
{
    private $pluginName;

    public function __construct(string $path, string $pluginName = '')
    {
        parent::__construct($path);

        $this->pluginName = $pluginName;
    }

    public function getControllerName(): string
    {
        return $this->getBasename();
    }

    public function isPlugin(): bool
    {
        return !empty($this->pluginName);
    }

    public function getPluginName(): string
    {
        return $this->pluginName;
    }

    /**
     * @return string[]
     */
    public function getUseModelSymbols(): array
    {
        if (is_null($property = $this->ast->getProperty('uses'))) {
            return [];
        }

        $modelSymbols = [];
        if (!is_null($property->default) && $property->default instanceof Array_) {
            foreach ($property->default->items as $item) {
                if ($item instanceof ArrayItem) {
                    if (!is_null($item->key) && $item->key instanceof String_) {
                        $modelSymbols[] = $item->key->value;
                    } elseif ($item->value instanceof String_) {
                        $modelSymbols[] = $item->value->value;
                    }
                }
            }
        }

        return $modelSymbols;
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

    public function flush(): void
    {
        $this->ast->flush();
    }
}
