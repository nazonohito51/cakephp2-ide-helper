<?php

\App::uses('AppModel', 'Model');

/**
 * This is summary.
 *
 * This is
 * description.
 *
 * @property \CakePhp2IdeHelper\Generator hoge
 * @mixin \CakePhp2IdeHelper\SomeBehavior1Behavior
 */
class SomeModel1 extends AppModel
{
    public $actsAs = [
        'SomeBehavior1' => [
            'option' => 'value'
        ],
        'SomeBehavior2',
        'SomePlugin1.SomeBehavior3'
    ];
}
