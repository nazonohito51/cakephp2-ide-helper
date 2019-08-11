<?php

namespace CakePhp2IdeHelper\PhpStormMeta;

class OverRideEntry
{
    private $target;
    private $map = [];

    public function __construct(string $target)
    {
        $this->target = $target;
    }

    public function add(string $key, string $value)
    {
        $this->map[$key] = $value;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function getMap()
    {
        return $this->map;
    }
}
