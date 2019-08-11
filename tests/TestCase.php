<?php
declare(strict_types=1);

namespace Tests;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function fixturePath(string $path = ''): string
    {
        $fixtureBase = __DIR__ . '/fixtures';
        $path = substr($path, 0, 1) === '/' ? $path : "/{$path}";

        return $fixtureBase . $path;
    }

    protected function fixtureAppPath(string $path = ''): string
    {
        $path = substr($path, 0, 1) === '/' ? $path : "/{$path}";

        return $this->fixturePath('cake2AppSample/app' . $path);
    }
}
