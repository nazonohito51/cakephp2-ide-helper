<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\PhpStormMeta;

use Barryvdh\Reflection\DocBlock;
use Barryvdh\Reflection\DocBlock\Serializer as DocBlockSerializer;
use Barryvdh\Reflection\DocBlock\Tag;
use CakePhp2IdeHelper\CakePhp2Analyzer\Readers\ControllerReader;
use CakePhp2IdeHelper\Exception\FailedUpdatingPhpDocException;

class UpdateControllerDocEntry
{
    private $controllerReader;
    private $replaceDoc;
    private $serializer;
    private $haveUpdate = false;

    public function __construct(ControllerReader $controllerReader)
    {
        $this->controllerReader = $controllerReader;

        $originalDocComment = $controllerReader->getPhpDoc() ?? '';
        $this->replaceDoc = new DocBlock($originalDocComment);
        $this->serializer = new DocBlockSerializer();
    }

    public function getControllerPath(): string
    {
        return $this->controllerReader->getRealPath();
    }

    public function appendTagWhenNotExist(string $tagString): void
    {
        $tag = Tag::createInstance("{$tagString} Added by cakephp2-ide-helper", $this->replaceDoc);

        $exist = false;
        foreach ($this->replaceDoc->getTags() as $existTag) {
            if ($existTag->__toString() === $tag->__toString()) {
                $exist = true;
                break;
            }
        }
        if (!$exist) {
            $this->replaceDoc->appendTag($tag);
            $this->haveUpdate = true;
        }
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
        if (!$this->phpDocIsEmpty() && $this->haveUpdate) {
            $replacedContent = $this->getReplaceModelContent();
            $file = new \SplFileObject($this->getModelPath(), 'w');
            $file->fwrite($replacedContent);
        }
    }
}
