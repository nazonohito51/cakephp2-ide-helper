<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\PhpStormMeta;

use Barryvdh\Reflection\DocBlock;
use Barryvdh\Reflection\DocBlock\Serializer as DocBlockSerializer;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ModelReader;
use CakePhp2IdeHelper\Exception\FailedUpdatingPhpDocException;

class UpdateModelDocEntry
{
    private $modelReader;
    private $replaceDoc;
    private $serializer;

    public function __construct(ModelReader $modelReader, DocBlock $replaceDoc)
    {
        $this->modelReader = $modelReader;
        $this->replaceDoc = $replaceDoc;
        $this->serializer = new DocBlockSerializer();
    }

    public function getModelPath(): string
    {
        return $this->modelReader->getRealPath();
    }

    public function getReplaceDocComment(): string
    {
        return $this->serializer->getDocComment($this->replaceDoc);
    }

    public function getReplaceModelContent(): string
    {
        $replaceDocComment = $this->getReplaceDocComment();
        if (empty($this->replaceDoc->getShortDescription()) && empty($this->replaceDoc->getLongDescription()->getContents())) {
            // remove summary, description
            $replaceDocComment = str_replace("/**\n * \n *", '/**', $replaceDocComment);
        }

        $originalContent = $this->modelReader->getContent();
        if (!empty($originalDocComment = $this->modelReader->getPhpDoc())) {
            $replacedContents = str_replace($originalDocComment, $replaceDocComment, $originalContent);
        } else {
            $needle = "class {$this->modelReader->getModelName()}";
            $replace = "{$replaceDocComment}\nclass {$this->modelReader->getModelName()}";
            $pos = strpos($originalContent, $needle);
            if ($pos === false) {
                throw new FailedUpdatingPhpDocException($this->getModelPath());
            }
            $replacedContents = substr_replace($originalContent, $replace, $pos, strlen($needle));
        }

        return $replacedContents;
    }

    private function phpDocIsEmpty(): bool
    {
        return $this->getReplaceDocComment() === "/**\n * \n *\n */";
    }

    public function update(): void
    {
        if (!$this->phpDocIsEmpty()) {
            $replacedContent = $this->getReplaceModelContent();
            $file = new \SplFileObject($this->getModelPath(), 'w');
            $file->fwrite($replacedContent);
        }
    }
}
