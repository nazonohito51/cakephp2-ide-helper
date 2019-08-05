<?php

namespace CakePhp2IdeHelper;

class Generator
{
    private $analyzer;

    public function __construct($appDir)
    {
        $this->analyzer = new Analyzer(new CakePhp2App($appDir));
    }

    public function generate()
    {
        $phpstormMetaFile = new \SplFileObject(__DIR__ . '/../phpstorm.meta.php', 'w');

        $phpstormMetaFile->fwrite($this->generatePhpStormMetaFileContent());
    }

    public function generatePhpStormMetaFileContent()
    {
        $overrideEntry = new OverRideEntry('\\ClassRegistry::init(0)');
        foreach ($this->analyzer->getModelReaders() as $modelReader) {
            $overrideEntry->add($modelReader->getSymbol(), $modelReader->getModelName());
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
