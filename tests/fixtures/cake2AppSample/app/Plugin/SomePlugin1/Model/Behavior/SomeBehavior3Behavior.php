<?php

class SomeBehavior3Behavior extends SomeBehavior1Behavior
{
    public function someBehavior3Method1(Model $model, bool $arg1, bool $args2): bool
    {
        return true;
    }

    public function someBehavior3Method2(Model $model, int $arg1, int $args2): int
    {
        return 1;
    }

    public function someBehavior3Method3(Model $model, string $arg1, string $args2): string
    {
        return 'str';
    }
}
