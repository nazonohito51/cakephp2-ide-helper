<?php

\App::uses('AppModel', 'Model');

class SomeModel4 extends AppModel
{
    public $actsAs = ['SomePlugin1.SomeBehavior3'];
}
