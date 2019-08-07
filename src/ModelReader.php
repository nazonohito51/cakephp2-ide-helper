<?php

namespace CakePhp2IdeHelper;

class ModelReader
{
    private $file;
    private $pluginName;

    public function __construct(string $path, string $pluginName = '')
    {
        if (!is_file($path)) {
            throw new \InvalidArgumentException('invalid model path: ' . $path);
        }

        $this->file = new \SplFileInfo($path);
        $this->pluginName = $pluginName;
    }

    public function getModelName(): string
    {
        return preg_replace('/\.php$/', '', $this->file->getFilename());
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
}
