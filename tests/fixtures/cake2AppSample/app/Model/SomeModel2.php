<?php

\App::uses('AppModel', 'Model');

class SomeModel2 extends AppModel
{
    public $actsAs = ['SomePlugin1.SomeBehavior3'];
}
