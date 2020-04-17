Core Extension for Yii2
==========================

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist ttungbmt/yii2-core "*"
```

Usage
-----
Overwrite Core

```php
require __DIR__ . '/../vendor/ttungbmt/yii2-core/src/config/bootstrap.php'; // Overwrite Core
require __DIR__ . '/../common/config/bootstrap.php';
require __DIR__ . '/../backend/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../vendor/ttungbmt/yii2-core/src/config/main.php', // Overwrite Core
    require __DIR__ . '/../common/config/main.php',
    require __DIR__ . '/../common/config/main-local.php',
    require __DIR__ . '/../backend/config/main.php',
    require __DIR__ . '/../backend/config/main-local.php'
);
```