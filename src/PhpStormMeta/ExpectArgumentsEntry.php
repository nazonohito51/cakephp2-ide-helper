<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\PhpStormMeta;

class ExpectArgumentsEntry
{
    private $target;
    private $argPosition;
    private $expectArgs = [];
    private $expectArgsAsString = [];

    public function __construct(string $target, int $argPosition)
    {
        $this->target = $target;
        $this->argPosition = $argPosition;
    }

    public function add(string $arg): void
    {
        if (!in_array($arg, $this->expectArgs)) {
            $this->expectArgs[] = $arg;
        }
    }

    public function addAsString(string $arg): void
    {
        if (!in_array($arg, $this->expectArgsAsString)) {
            $this->expectArgsAsString[] = $arg;
        }
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getArgPosition(): int
    {
        return $this->argPosition;
    }

    public function getArgs(): array
    {
        $ret = [];
        foreach ($this->expectArgs as $expectArg) {
            $ret[] = $expectArg;
        }
        foreach ($this->expectArgsAsString as $expectArg) {
            $ret[] = "'$expectArg'";
        }
        return $ret;
    }

    public function isLastArg(string $arg): bool
    {
        $args = $this->getArgs();
        return end($args) === $arg;
    }
}
