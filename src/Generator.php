<?php

namespace CakePhp2IdeHelper;

class Generator
{
    private $rootDir;
    private $analyzer;

    public function __construct($rootDir, $appDir)
    {
        $this->analyzer = new Analyzer(new CakePhp2App($appDir));
        $this->rootDir = $rootDir;
    }

    public function generate()
    {
        $phpstormMetaFile = new \SplFileObject($this->rootDir . '/.phpstorm.meta.php', 'w');

        $phpstormMetaFile->fwrite($this->generatePhpStormMetaFileContent());
    }

    public function generatePhpStormMetaFileContent()
    {
        $overrideEntries = array(new OverRideEntry('\\ClassRegistry::init(0)'));
        foreach ($this->analyzer->getModelReaders() as $modelReader) {
            $overrideEntries[0]->add($modelReader->getSymbol(), $modelReader->getModelName());
        }

        $content = '<?php' . "\n";

        ob_start();
        include $this->getMetaFileTemplatePath();
        $content .= ob_get_contents();
        ob_end_clean();

        return $content;
    }

    private function getMetaFileTemplatePath()
    {
        return __DIR__ . '/../resources/phpstorm.meta.template.php';
    }
}
