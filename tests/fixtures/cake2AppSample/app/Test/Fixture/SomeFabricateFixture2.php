<?php

use Fabricate\Fabricate;

\App::uses('AppTestFixture', 'Test/Fixture');

class SomeFabricateFixture2 extends AppTestFixture
{
    public $import = [
        'file' => 'default',
        'schema' => 'some_fabricate_fixture2',
    ];

    public function init()
    {
        Fabricate::define('SomeModel2', function ($data, $world) {
            return [
            ];
        });

        Fabricate::define(['SomeModel2_extends', 'parent' => 'SomeModel2'], function ($data, $world) {
            return [
            ];
        });
    }
}
