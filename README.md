# cakephp2-ide-helper

## Installation
```shell script
composer global require "nazonohito51/cakephp2-ide-helper"
```

Recommend adding `.gitignore_global` or `.gitignore`.

```
.phpstorm.meta.php
_ide_helper.php
```

## Basic Usage
### `generate:meta`
```shell script
path/to/your/vendor/bin/cakephp2-ide-helper generate:meta --app-dir=./app
```

Generate `.phpstorm.meta.php`. [see sample](https://github.com/nazonohito51/cakephp2-ide-helper/blob/master/sample/.phpstorm.meta.php).

### `generate:helper`
```shell script
path/to/your/vendor/bin/cakephp2-ide-helper generate:helper --app-dir=./app
```

Generate `.phpstorm.meta.php`. [see sample](https://github.com/nazonohito51/cakephp2-ide-helper/blob/master/sample/.phpstorm.meta.php).

### `generate:model`
```shell script
path/to/your/vendor/bin/cakephp2-ide-helper generate:model --app-dir=./app --git-root=./
```

Update phpdoc of models, for code complement of behavior methods.
If there is duplicate models, this command will be failed.
When this case, you need `--ignore` options.

```shell script
path/to/your/vendor/bin/cakephp2-ide-helper generate:model --app-dir=./app --git-root=./ --ignore=Model/SomeDuplicateClass.php
```

## TODO
- [x] read actsAs from Model
- [x] read public methods from Behavior
- [x] update phpdoc on Model with diff(use str_replace)
- [x] analyze return type of Behavior
- [x] deprecate Behavior method by model extends graph
- [x] consider git manage files
- [x] skip update model when phpdoc is empty
- [x] ClassRegistry argument helper
- [x] Fabricate argument helper
- [ ] fixtures argument helper(phpstorm meta)
- [x] model property in controller
- [ ] getDataSource
- [x] Model::find('first', 'count' ...)
- [x] Model::find('', ['condition'...])
- [x] withRead argument
- [ ] ignore unload behavior
- [x] disable behavior extend
- [ ] Controller uses property
- [x] Consider Behavior extends
- [x] ignore abstract behavior, or assign abstract keyword
- [x] consider about duplicate behaviors
