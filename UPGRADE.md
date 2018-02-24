# Upgrading from v2.x to v3.x

Configula v3 is quite different from v2.  But, you can replicate the behavior of Configula v2 
with minimal code changes by using `Configula\ConfigFactory`:

## Loading configuration

Before upgrade: 

```php
use Configula\Config;

$config = new Config('/path/to/config/files', ['default' => 'values']);
```

After upgrade:

```php
use Configula\ConfigFactory;

$config = ConfigFactory::loadPath('/path/to/config/files', ['default' => 'values']);
```

One behavior change in v3 is that `loadPath()` will now recursively load configuration files from your configuration
path.  If you want to read configuration files only from the top-level directory of your config path, you can do the
following:

```php
use Configula\ConfigFactory;

$config = ConfigFactory::loadSingleDirectory('/path/to/config/files');
```

## Getting an array copy of values

Before upgrade:

```php
$config->getItems();
```

After upgrade:

```php
$config->getArrayCopy();
```