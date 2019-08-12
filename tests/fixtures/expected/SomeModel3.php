<?php

\App::uses('AppModel', 'Model');

/**
 * @mixin \CakePhp2IdeHelper\SomeBehavior2 Added by cakephp2-ide-helper
 */
class SomeModel3 extends AppModel
{
    public $actsAs = ['SomeBehavior2'];
}
