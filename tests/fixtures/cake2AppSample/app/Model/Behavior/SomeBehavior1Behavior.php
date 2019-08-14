<?php

class SomeBehavior1Behavior extends ModelBehavior
{
    public function someBehavior1Method1(Model $model, bool $arg1, bool $args2): bool
    {
        return true;
    }

    public function someBehavior1Method2(Model $model, int $arg1, int $args2): int
    {
        return 1;
    }

    public function someBehavior1Method3(Model $model, string $arg1, string $args2): string
    {
        return 'str';
    }

    public function setup(Model $model, $config = array())
    {
    }

    public function cleanup(Model $model)
    {
        if (isset($this->settings[$model->alias])) {
            unset($this->settings[$model->alias]);
        }
    }

    public function beforeFind(Model $model, $query)
    {
        return true;
    }

    public function afterFind(Model $model, $results, $primary = false)
    {
    }

    public function beforeValidate(Model $model, $options = array())
    {
        return true;
    }

    public function afterValidate(Model $model)
    {
        return true;
    }

    public function beforeSave(Model $model, $options = array())
    {
        return true;
    }

    public function afterSave(Model $model, $created, $options = array())
    {
        return true;
    }

    public function beforeDelete(Model $model, $cascade = true)
    {
        return true;
    }

    public function afterDelete(Model $model)
    {
    }

    public function onError(Model $model, $error)
    {
    }
}
