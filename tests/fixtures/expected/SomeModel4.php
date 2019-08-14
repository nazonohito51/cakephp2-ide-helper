<?php

\App::uses('AppModel', 'Model');

/**
 * @mixin \CakePhp2IdeHelper\SomeBehavior3Behavior Added by cakephp2-ide-helper
 */
class SomeModel4 extends AppModel
{
    public $actsAs = ['SomePlugin1.SomeBehavior3'];
}
