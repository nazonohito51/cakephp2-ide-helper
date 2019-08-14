<?php

\App::uses('AppModel', 'Model');

class SomeModel5 extends AppModel
{
    public $actsAs = [];

    public function someMethod()
    {
        $this->Behaviors->unload('SomeBehavior2');
    }
}
