<?php

use Fabricate\Fabricate;

\App::uses('AppTestFixture', 'Test/Fixture');

class SomeFabricateFixture1 extends AppTestFixture
{
    public $import = [
        'file' => 'default',
        'schema' => 'some_fabricate_fixture1',
    ];

    public function init()
    {
        Fabricate::define(['SomeModel1', 'class' => 'SomeModel1'], function ($data, $world) {
            return [
            ];
        });

        Fabricate::define(['SomeModel1_Extends', 'parent' => 'SomeModel1'], function ($data, $world) {
            return [
            ];
        });
    }
}
