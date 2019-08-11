<?php

\App::uses('AppModel', 'Model');

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
