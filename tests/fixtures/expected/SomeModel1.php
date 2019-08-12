<?php

\App::uses('AppModel', 'Model');

/**
 * This is summary.
 *
 * This is
 * description.
 *
 * @property \CakePhp2IdeHelper\Generator hoge
 * @mixin \CakePhp2IdeHelper\SomeBehavior1
 * @mixin \CakePhp2IdeHelper\SomeBehavior1 Added by cakephp2-ide-helper
 * @mixin \CakePhp2IdeHelper\SomeBehavior2 Added by cakephp2-ide-helper
 * @mixin \CakePhp2IdeHelper\SomeBehavior3 Added by cakephp2-ide-helper
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
