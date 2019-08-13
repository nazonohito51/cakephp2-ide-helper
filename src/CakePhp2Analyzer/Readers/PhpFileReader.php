<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\CakePhp2Analyzer\Readers;

use CakePhp2IdeHelper\PhpParser\Ast;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar\String_;

class PhpFileReader
{
    protected $file;
    protected $ast;

    public function __construct(string $path)
    {
        if (!is_file($path)) {
            throw new \InvalidArgumentException('invalid model path: ' . $path);
        }

        $this->file = new \SplFileObject($path);
        $this->ast = new Ast($path);
    }

    public function getBasename(): string
    {
        return $this->file->getBasename('.php');
    }

    public function getRealPath(): string
    {
        return $this->file->getRealPath();
    }

    public function getContent(): string
    {
        return file_get_contents($this->getRealPath());
    }
}