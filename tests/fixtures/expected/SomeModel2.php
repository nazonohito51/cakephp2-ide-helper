<?php

\App::uses('AppModel', 'Model');

/**
 * @mixin \CakePhp2IdeHelper\SomeBehavior3 Added by cakephp2-ide-helper
 */
class SomeModel2 extends AppModel
{
    public $actsAs = ['SomePlugin1.SomeBehavior3'];
}
