<?php

namespace CakePhp2IdeHelper\PhpStormMeta;

class ExpectArgumentsEntry
{
    private $target;
    private $argPosition;
    private $expectArgs = [];

    public function __construct(string $target, int $argPosition)
    {
        $this->target = $target;
        $this->argPosition = $argPosition;
    }

    public function add(string $arg)
    {
        $this->expectArgs[] = $arg;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function getArgPosition()
    {
        return $this->argPosition;
    }

    public function getArgs()
    {
        return $this->expectArgs;
    }
}
