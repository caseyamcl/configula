# Upgrading from v2.x to v3.x

Configula v3 is quite different from v2.  But, you can replicate the behavior of Configula v2 
with minimal code changes as follows:

## Class name changes

`Configula\Config` has now become `Config\ConfigValues`.  Update all references.

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

$config = ConfigFactory::loadSingleDirectory('/path/to/config/files', ['default' => 'values']);
```

## Adding configuration to an existing instance

Before upgrade:

```php
$config->loadConfig('/some/path');
```

After upgrade:

```php
// Note that the ConfigValues class is now immutable, so you need to use the instance that
// is returned from the merge method.
$config = $config->merge((new DecidingFileLoader('/some/path')->load()));
```

## Getting a value

Before upgrade:

```php
$config->getItem('some_item', 'default');
```

After upgrade:

```php
// same behavior as v2 getItem()
$config->get('some_item', 'default');

// returns NULL
$config->find('non_existent_item'); 

// throws exception
$config->get('non_existent_item'); 
```

## Checking if a value exists

Before upgrade:

```php
$config->valid('some_item');
```

After upgrade:

```php
$config->has('some_item');
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