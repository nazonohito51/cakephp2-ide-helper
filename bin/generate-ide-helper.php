<?php
require_once __DIR__ . '/../vendor/autoload.php';

if ($argc < 2) {
    throw new InvalidArgumentException('cakephp2 app dir required.');
}

$generator = new \CakePhp2IdeHelper\Generator($argv[1]);

$generator->generate();
