<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\CakePhp2Analyzer\StructuralElements;

class CakePhp2App extends CakePhp2Dir
{
    /**
     * @var string[]
     */
    private $behaviorDirs = [];

    /**
     * @var string[]
     */
    private $pluginDirs = [];

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

    public function addPluginDir(string $pluginDir): void
    {
        if (!is_dir($pluginDir)) {
            throw new \InvalidArgumentException();
        }

        $this->pluginDirs[] = $pluginDir;
    }

    /**
     * @return CakePhp2Plugin[]
     */
    public function getPlugins(): array
    {
        $ret = [];

        $defaultPluginDirs = [
            $this->appDir . '/Plugin',
            $this->appDir . '/../plugins',
            $this->appDir . '/Vendor/cakephp/cakephp/lib/Cake/plugins'
        ];
        foreach (array_merge($defaultPluginDirs, $this->pluginDirs) as $pluginDir) {
            $pluginDir = substr($pluginDir, -1) === '/' ? $pluginDir : "{$pluginDir}/";
            if (is_dir($pluginDir)) {
                foreach (glob("{$pluginDir}*", GLOB_ONLYDIR) as $pluginPath) {
                    $pluginDir = new \SplFileInfo($pluginPath);
                    if ($pluginDir->isDir()) {
                        $ret[] = new CakePhp2Plugin($pluginDir->getRealPath(), $pluginDir->getFilename());
                    }
                }
            }
        }

        return $ret;
    }

    public function addBehaviorDir(string $behaviorDir): void
    {
        if (!is_dir($behaviorDir)) {
            throw new \InvalidArgumentException();
        }

        $this->behaviorDirs[] = $behaviorDir;
    }

    /**
     * @return string[]
     */
    protected function getBehaviorFiles(): array
    {
        $ret = parent::getBehaviorFiles();

        foreach ($this->behaviorDirs as $behaviorDir) {
            foreach (glob($behaviorDir . '/*.php') as $path) {
                $ret[] = $path;
            }
        }

        return $ret;
    }
}
