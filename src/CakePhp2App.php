<?php

namespace CakePhp2IdeHelper;

class CakePhp2App extends CakePhp2Dir
{
    /**
     * @inheritDoc
     */
    public function isPlugin(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getPluginName(): ?string
    {
        return null;
    }

    /**
     * @return CakePhp2Plugin[]
     */
    public function getPluginDirs(): array
    {
        $ret = [];
        foreach (glob($this->appDir . '/Plugin/*', GLOB_ONLYDIR) as $pluginPath) {
            $pluginDir = new \SplFileInfo($pluginPath);
            if ($pluginDir->isDir()) {
                $ret[] = new CakePhp2Plugin($pluginDir->getRealPath(), $pluginDir->getFilename());
            }
        }

        return $ret;
    }
}
