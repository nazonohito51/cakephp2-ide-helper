<?php

\App::uses('AppModel', 'Model');

class SomeModel3 extends AppModel
{
    public $actsAs = ['SomeBehavior2'];
}
