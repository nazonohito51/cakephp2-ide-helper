<?php

\App::uses('Model', 'Model');

/**
 * @mixin \CakePhp2IdeHelper\SomeBehavior1Behavior Added by cakephp2-ide-helper
 */
class AppModel extends Model
{
    public $actsAs = ['SomeBehavior1'];
}
