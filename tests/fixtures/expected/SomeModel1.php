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
 * @mixin \CakePhp2IdeHelper\SomeBehavior1Behavior Added by cakephp2-ide-helper
 * @mixin \CakePhp2IdeHelper\SomeBehavior2Behavior Added by cakephp2-ide-helper
 * @mixin \CakePhp2IdeHelper\SomeBehavior3Behavior Added by cakephp2-ide-helper
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
