<?php

namespace PHPSTORM_META {
    override(
        \ClassRegistry::init(0),
        map(
            array(
                'AppModel' => 'AppModel',
                'SomeModel1' => 'SomeModel1',
                'SomeModel2' => 'SomeModel2',
                'SomePlugin1.SomeModel3' => 'SomeModel3',
                'SomePlugin1.SomeModel4' => 'SomeModel4',
            )
        )
    );

    expectedArguments(
        \ClassRegistry::init(),
        0,
        'AppModel',
        'SomeModel1',
        'SomeModel2',
        'SomePlugin1.SomeModel3',
        'SomePlugin1.SomeModel4',
    );
    expectedArguments(
        \Fabricate\Fabricate::create(),
        0,
        'SomeModel1',
        'SomeModel1_Extends',
        'SomeModel2',
        'SomeModel2_extends',
    );
    expectedArguments(
        \Model::find(),
        0,
        'first',
        'count',
        'all',
        'list',
        'threaded',
        'neighbors',
    );
}
