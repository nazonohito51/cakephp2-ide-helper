<?php

namespace CakePhp2IdeHelper;

class ModelReader
{
    private $file;
    private $pluginName;

    public function __construct($path, $pluginName = '')
    {
        if (!is_file($path)) {
            throw new \InvalidArgumentException('invalid model path: ' . $path);
        }

        $this->file = new \SplFileInfo($path);
        $this->pluginName = $pluginName;
    }

    /**
     * @return string
     */
    public function getModelName()
    {
        return preg_replace('/\.php$/', '', $this->file->getFilename());
    }

    /**
     * @return bool
     */
    public function isPlugin()
    {
        return !empty($this->pluginName);
    }

    /**
     * @return string
     */
    public function getPluginName()
    {
        return $this->pluginName;
    }

    /**
     * @return string
     */
    public function getSymbol()
    {
        if ($this->isPlugin()) {
            return "{$this->getPluginName()}.{$this->getModelName()}";
        }

        return $this->getModelName();
    }
}
