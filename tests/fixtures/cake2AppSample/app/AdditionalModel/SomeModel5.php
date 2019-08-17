<?php

\App::uses('AppModel', 'Model');

class SomeModel5 extends SomeModel1
{
    public $actsAs = [];

    public function someMethod()
    {
        $this->Behaviors->unload('SomeBehavior2');
    }
}
