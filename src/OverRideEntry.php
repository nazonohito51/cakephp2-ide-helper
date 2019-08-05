<?php

namespace CakePhp2IdeHelper;

class OverRideEntry
{
    private $target;
    private $map = array();

    public function __construct($target)
    {
        $this->target = $target;
    }

    public function add($key, $value)
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
