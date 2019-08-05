<?php

namespace PHPSTORM_META {  // we want to avoid the pollution
	// this is a saner and self-documented format for PhpStorm 2016.2 and later
	// Try QuickDoc on these "magic" functions, or even Go to definition!
	override(
		\ServiceLocatorInterface::get(0),         // method signature //argument number is ALWAYS 0 now.
		map(
			[ //map of argument value -> return type
				"special" => \Exception::class,                //Reference target classes by ::class constant
				\ExampleFactory::EXAMPLE_B => ExampleB::class,  // FYI, we can now support class constant argument values
				\EXAMPLE_B => \ExampleB::class,              // and global constants too
				//non-mapped value, e.g. $getByClassNameConst case above will be returned automatically
			]
		)
	);

	override(
		\ClassRegistry::init(0),         // method signature //argument number is ALWAYS 0 now.
		map(
			[ //map of argument value -> return type
				'Plugin.SomeModel1' => 'SomeModel1',                //Reference target classes by ::class constant
				'SomeModel2' => 'SomeModel2',
				\ExampleFactory::EXAMPLE_B => ExampleB::class,  // FYI, we can now support class constant argument values
				\EXAMPLE_B => \ExampleB::class,              // and global constants too
				//non-mapped value, e.g. $getByClassNameConst case above will be returned automatically
			]
		)
	);

	expectedArguments(
		\AppModel::withRead(),
		0,

	);
}
