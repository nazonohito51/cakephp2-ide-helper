<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\CakePhp2Analyzer\Readers;

use CakePhp2IdeHelper\PhpParser\Ast;
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\PrettyPrinter\Standard;

class BehaviorReader
{
    private $file;
    private $ast;
    private $pluginName;

    public function __construct(string $path, string $pluginName = '')
    {
        if (!is_file($path)) {
            throw new \InvalidArgumentException('invalid model path: ' . $path);
        }

        $this->file = new \SplFileInfo($path);
        $this->ast = new Ast($path);
        $this->pluginName = $pluginName;
    }

    public function getBehaviorName(): string
    {
        return $this->file->getBasename('.php');
    }

    public function getBehaviorNameWithoutSuffix(): string
    {
        return preg_replace('/Behavior$/', '', $this->getBehaviorName());
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
            return "{$this->getPluginName()}.{$this->getBehaviorNameWithoutSuffix()}";
        }

        return $this->getBehaviorNameWithoutSuffix();
    }

    /**
     * @return ClassMethod[]
     */
    public function getPublicMethods(): array
    {
        $ret = [];

        $definedMethods = ['setup', 'cleanup', 'beforeFind', 'afterFind', 'beforeValidate', 'afterValidate', 'beforeSave', 'afterSave', 'beforeDelete', 'afterDelete', 'onError'];
        foreach ($this->ast->getPublicMethods() as $publicMethod) {
            if (!in_array($publicMethod->name->toString(), $definedMethods)) {
                $ret[] = clone $publicMethod;
            }
        }

        return $ret;
    }
}
