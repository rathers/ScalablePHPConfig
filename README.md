ScalablePHPConfig
=================

A fast, simple and scalable config class for PHP

Requirements
------------
Depends on APC user caching which is bundled with APC for PHP <= 5.4 or is installable separately via [APCu](https://github.com/krakjoe/apcu) for PHP 5.5

Example Usage
-------------
```php
$cfg = new Config("/etc/config.ini");
echo $cfg->get("database.username");
```
