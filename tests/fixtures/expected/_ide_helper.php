<?php

namespace CakePhp2IdeHelper;

class SomeBehavior1Behavior
{
    public function someBehavior1Method1(bool $arg1, bool $args2) : bool
    {
        /**
         * @var \SomeBehavior1Behavior $behavior
         * @var \Model $model
         */
        return $behavior->someBehavior1Method1($model, $arg1, $args2);
    }
    public function someBehavior1Method2(int $arg1, int $args2) : int
    {
        /**
         * @var \SomeBehavior1Behavior $behavior
         * @var \Model $model
         */
        return $behavior->someBehavior1Method2($model, $arg1, $args2);
    }
    public function someBehavior1Method3(string $arg1, string $args2) : string
    {
        /**
         * @var \SomeBehavior1Behavior $behavior
         * @var \Model $Model
         */
        return $behavior->someBehavior1Method3($Model, $arg1, $args2);
    }
}
class DeprecateSomeBehavior1Behavior
{
    /**
     * @deprecated
     */
    public function someBehavior1Method1(bool $arg1, bool $args2) : bool
    {
    }
    /**
     * @deprecated
     */
    public function someBehavior1Method2(int $arg1, int $args2) : int
    {
    }
    /**
     * @deprecated
     */
    public function someBehavior1Method3(string $arg1, string $args2) : string
    {
    }
}
class SomeBehavior2Behavior
{
    public function someBehavior2Method1(bool $arg1, bool $args2) : bool
    {
        /**
         * @var \SomeBehavior2Behavior $behavior
         * @var \Model $model
         */
        return $behavior->someBehavior2Method1($model, $arg1, $args2);
    }
    public function someBehavior2Method2(int $arg1, int $args2) : int
    {
        /**
         * @var \SomeBehavior2Behavior $behavior
         * @var \Model $model
         */
        return $behavior->someBehavior2Method2($model, $arg1, $args2);
    }
    public function someBehavior2Method3(string $arg1, string $args2) : string
    {
        /**
         * @var \SomeBehavior2Behavior $behavior
         * @var \Model $model
         */
        return $behavior->someBehavior2Method3($model, $arg1, $args2);
    }
}
class DeprecateSomeBehavior2Behavior
{
    /**
     * @deprecated
     */
    public function someBehavior2Method1(bool $arg1, bool $args2) : bool
    {
    }
    /**
     * @deprecated
     */
    public function someBehavior2Method2(int $arg1, int $args2) : int
    {
    }
    /**
     * @deprecated
     */
    public function someBehavior2Method3(string $arg1, string $args2) : string
    {
    }
}
class SomeBehavior3Behavior
{
    public function someBehavior3Method1(bool $arg1, bool $args2) : bool
    {
        /**
         * @var \SomeBehavior3Behavior $behavior
         * @var \Model $model
         */
        return $behavior->someBehavior3Method1($model, $arg1, $args2);
    }
    public function someBehavior3Method2(int $arg1, int $args2) : int
    {
        /**
         * @var \SomeBehavior3Behavior $behavior
         * @var \Model $model
         */
        return $behavior->someBehavior3Method2($model, $arg1, $args2);
    }
    public function someBehavior3Method3(string $arg1, string $args2) : string
    {
        /**
         * @var \SomeBehavior3Behavior $behavior
         * @var \Model $model
         */
        return $behavior->someBehavior3Method3($model, $arg1, $args2);
    }
}
class DeprecateSomeBehavior3Behavior
{
    /**
     * @deprecated
     */
    public function someBehavior3Method1(bool $arg1, bool $args2) : bool
    {
    }
    /**
     * @deprecated
     */
    public function someBehavior3Method2(int $arg1, int $args2) : int
    {
    }
    /**
     * @deprecated
     */
    public function someBehavior3Method3(string $arg1, string $args2) : string
    {
    }
}