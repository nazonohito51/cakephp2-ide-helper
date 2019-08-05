<?php

namespace CakePhp2IdeHelper;

class CakePhp2App extends CakePhp2Dir
{
    /**
     * @inheritDoc
     */
    public function isPlugin()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getPluginName()
    {
        return false;
    }

    /**
     * @return CakePhp2Plugin[]
     */
    public function getPluginDirs()
    {
        $ret = array();
        foreach (glob($this->appDir . '/Plugin') as $pluginPath) {
            $pluginDir = new \SplFileInfo($pluginPath);
            if ($pluginDir->isDir()) {
                $ret[] = new CakePhp2Plugin($pluginDir->getRealPath(), $pluginDir->getFilename());
            }
        }

        return $ret;
    }
}
