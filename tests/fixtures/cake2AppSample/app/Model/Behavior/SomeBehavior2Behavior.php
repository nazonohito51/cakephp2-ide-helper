<?php

class SomeBehavior2Behavior extends ModelBehavior
{
    public function someBehavior2Method1(Model $model, bool $arg1, bool $args2): bool
    {
        return true;
    }

    public function someBehavior2Method2(Model $model, int $arg1, int $args2): int
    {
        return 1;
    }

    public function someBehavior2Method3(Model $model, string $arg1, string $args2): string
    {
        return 'str';
    }
}
