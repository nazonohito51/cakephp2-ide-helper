<?php

namespace PHPSTORM_META {
    override(
        \ClassRegistry::init(0),
        map(
            array(
                'SomeModel1' => 'SomeModel1',
                'SomeModel2' => 'SomeModel2',,
                'Plugin1.SomeModel3' => 'SomeModel3',
                'Plugin1.SomeModel4' => 'SomeModel4',
                'Plugin2.SomeModel5' => 'SomeModel5',
                'Plugin2.SomeModel6' => 'SomeModel6',
            )
        )
    );
}
