<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\CakePhp2Analyzer\Readers;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;

class BehaviorReader extends PhpFileReader
{
    private $pluginName;

    public function __construct(string $path, string $pluginName = '')
    {
        parent::__construct($path);

        $this->pluginName = $pluginName;
    }

    public function getBehaviorName(): string
    {
        return $this->getBasename();
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
                $ret[] = $publicMethod;
            }
        }

        return $ret;
    }

    public function getParentBehaviorName(): ?string
    {
        $classLike = $this->ast->getClassLike();
        if ($classLike instanceof Class_ && !is_null($classLike->extends)) {
            $parent = $classLike->extends;
            if ($parent instanceof Name && $parent->toString() !== 'CakeObject') {
                return $parent->toString();
            }
        }

        return null;
    }
}
