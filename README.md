# Configula

Configula is a configuration library for PHP 7.1+.  

Use this library when you want to load configuration from the filesystem, environment, or other source.  It implements
your configuration values as an immutable object in PHP.

You can use it with [phpDotEnv](https://github.com/vlucas/phpdotenv), 
the [Symfony Config Component](http://symfony.com/doc/current/components/config/introduction.html), or as a 
standalone tool.

[![Build Status](https://travis-ci.org/caseyamcl/Configula.png?branch=master)](https://travis-ci.org/caseyamcl/Configula)

## Features

* Load configuration from a variety of sources:
    * Load values from _.php_, _.ini_, _.json_, and _.yml_ configuration file types
    * Load values from the environment, and _.env_ files using a DotEnv library ([vlucas](https://github.com/vlucas/phpdotenv) or [Symfony](https://github.com/symfony/dotenv))
    * Easily write your own loaders to support other file types and sources
* Cascade merge values from multiple sources (e.g. array, files, environment, etc) 
* Optionally use in combination with [Symfony Config Component](http://symfony.com/doc/current/components/config/introduction.html)
  to validate configuration values and/or cache them
* Provides an immutable object to access your configuration values in your application:
    * Array-access (read only)
    * Standard value `get(val)` and `has(val)` access
    * Magic method `__get(val)` and `__isset(val)` access
    * Implements `Iterator` and `Countable` interfaces
* Provides simple dot-based access to nested values (e.g. `$config->get('application.sitename.prefix');`)
* Code quality standards: PSR-2, 100% Unit Test Coverage
  
## Need PHP v5.* compatibility?

Configula Version 2.x is compatible with PHP v5.3+.  If you need PHP 5.x compatibility, instruct Composer to use
the **2.x** version of this library instead of the current one:

```
composer require caseyamcl/configula:^2.4
```

## Upgrading?

Refer to [UPGRADE.md](UPGRADE.md) for notes on upgrading from Version 2.x to Version 3.
 
## Quick Start
  
Simple usage:

```php
use Configula\ConfigFactory as Config;

//Access configuration values
$config = Config::loadPath('/path/to/config/files');
$some_value = $config->get('some_key');
```
        
Property-like access to your config settings:

```php
use Configula\ConfigFactory as Config;

//Access configuration values
$config = Config::loadPath('/path/to/config/files');
$some_value = $config->some_key;
```

Array and iterator access to your config settings:

```php
use Configula\ConfigFactory as Config;

//Access conifguration values
$config = Config::load('/path/to/config/files');

foreach ($config as $item => $value) {
    echo "<li>{$item} is {$value}</li>";
}
```

## Installation

### Installation via Composer:

```bash
composer require caseyamcl/configula
```

### Non-composer installation

This is not recommended, but it is possible: 

1. Add the `src/` directory to your application (`cp -r src/* /PATH/TO/YOUR/APP/configula`)
2. Either use `require` statements for ALL classes in this library, or use a 
   [PSR-4 compatible autoloader](http://www.php-fig.org/psr/psr-4/) like [this one](https://packagist.org/packages/keradus/psr4autoloader).

## Basic usage

1.  Create a folder in your application for storing configuration files.
2.  Populate the folder with configuration files.  See [Config Folder Layout](#config-folder-layout) section 
    below for more details.
3.  Instantiate a Configula instance, and pass the path as the first parameter:

        $config = \Configula\ConfigFactory::loadPath('/path/to/app/config');

4.  Configuration values become properties of the Configula object (if the value does not exist, an exception is thrown):  

        $myValue = $config->my_config_setting;

5.  Alternatively, use the `get()` method, which accepts an optional default value:

        $myValue = $config->getItem('my_config_setting', 'default_to_fall_back_on');

6.  Finally, you can access the object as if it were an array (if the value does not exist, an exception is thrown):

        $myValue = $config['my_config_setting'];

7. Nested values are accessible using dot notation:

        $myValue = $config['dbs.default.host'];
        // or...
        $myValue = $config->get('dbs.default.host');

7.  `foreach()` and `count()` also work, since Configula implements those interfaces:

        foreach ($config as $settingName => $settingValue) {
            echo "{$settingName} is {$settingValue}";
        }
        echo "There are " . count($config) . " settings total";

8.  To get all config settings as an array, use the `getArrayCopy()` method:

        $allValues = $config->getArrayCopy();

9.  If you would like to preload the config object with default values, pass an array as the second parameter to the
    `loadPath` method:

        //Values in the config files will override the default values
        $defaults = ['foo' => 'bar', 'baz' => 'biz'];
        $config = \Configula\ConfigFactory::loadPath('/path/to/app/config', $defaults);

10. If you would like to use Configula with only default values, use the `build` method:

        //The default values will be the only config options available
        $values = array('foo' => 'bar', 'baz' => 'biz');
        $config = \Configula\ConfigFactory::build($values);

### Immutability

The `ConfigValues` object, once instantiated, is immutable, meaning that it is read-only.  You can not alter the config 
values.  You can, however, merge values into a new copy of the object using `ConfigValues::merge()` method.

```php
$values = array('foo' => 'bar', 'baz' => 'biz');
$config = \Configula\ConfigFactory::build($values);

$config = $config->mergeValues(['another' => 'value']);

// Config new contains
// 'foo', 'baz', and 'another' items

// You can also merge any ConfigValues instance 
$config = $config->merge(\Configula\ConfigFactory::path('/some/path'));

```

### Invalid Code

If any configuration file contains invalid code (invalid PHP or malformed JSON, for example), the Configula class will 
throw a `\Configula\Exception\ConfigParseException()`

## Default Behavior

Prior versions of this library were very opinionated about what values would be loaded.  This behavior is provided in
the `ConfigFactory::loadPath()` loader.

### Config Folder Layout

By default, Configula will load files with the following extensions:

* `php` - Configula will look for an array called `$config` in this file.
* `json` - Uses the built-in PHP `json_decode()` function
* `yaml` or `yml` - Uses the [Symfony YAML parser](https://symfony.com/doc/current/components/yaml.html)
* `ini` - Uses the built-in PHP `parse_ini_file()` function

The `ConfigFactory::loadPath()` loader will traverse directories in your configuration path recursively.  If you want
to override this behavior, see below.

### Local Configuration Files

In some cases, you may want to have local configuration files that override the default configuration files.  To
override any configuration file, create another configuration file, and append `.local.EXT` to the end.

For example, a configuration file named `database.yml` is overridden by `database.local.yml`, if the latter file exists.

This is very useful if you want certain settings included in version control; just add `/path/to/config/*.local.EXT` 
to your `.gitignore` or equivalent VCS file.

### Example

Consider the following directory layout

```
/my/app/config
 ├config.php
 ├config.local.php
 └/subfolder
  ├database.yml
  └database.local.yml	
```

If you use `ConfigFactory::loadPath('/my/app/config')`, the files will be parsed according to their extension and
values will be merged in the following order (values in files later in the list clobber earlier values):

```
- /config.php
- /subfolder/database.yml
- /config.local.php
- /subfolder/database.local.yml
```

## Handling Errors

All exceptions extend `Configula\Exception\ConfigException`.  You can catch this exception to catch all possible errors
thrown during loading and reading of configuration values.

* Improper use of this library will generate a `Configula\Exception\ConfigLogicException`.
* If something goes wrong when loading configuration values, a `Config\Exception\ConfigLoaderException` is thrown.
* If you attempt to read a configuration value that does not exist, and you have not provided a default value at
  runtime, a `Configula\Exception\ConfigValueNotFoundException` is thrown.
  
```php
// All of these throw a ConfigValueNotFoundException
$config->get('non_existent_value');
$config['non_existent_value'];
$config->non_existent_value;

// This will not throw an exception if the value doesn't exist
$config->get('non_existent_value', null);
```

## Advanced Loading Options

You can load configuration from any source, using any strategy you wish.  Configula comes with pre-built support
for a few common strategies, or you can create your own combination of loaders to load configuration however you wish.

### Using the `ConfigFactory::loadMultiple()` method

### Loading environment variables

todo: this..

### Interpreting values from an `.env` file

todo: this..

### Mixing and matching loaders

todo: this..

## Extending the `ConfigValues` class to make accessing configuration easy

While it is entirely possible to use the `Configula\ConfigValues` class directly , you might also want to provide 
some application-specific methods to load configuration values.  This provides a better developer experience. 

```php
use Configula\ConfigValues;
use Configula\Exception\InvalidConfigValueException;

class AppConfig extends ConfigValues
{
    /**
     * Is the app running in development mode?
     *
     * @return bool
     */
    public function isDevMode(): bool
    {
        // Get the value or assume false
        return (bool) $this->get('devmode', false);
    }
    
    /**
     * Get the encryption key (as 32-character alphanumeric string)
     *
     * @return string
     */
    public function getEncryptionKey(): string
    {
        // If the value doesn't exist, a `ConfigValueNotFoundException` is thrown
        $key = $this->get('encryption_key');
        
        // Let's do a little validation...
        if (strlen($key) != 32) {
            throw new InvalidConfigValueException('Encryption key must be 32 characters');
        }
        
        return $key;
    }
}
```

*Note:* Notice that the example above uses the `InvalidConfigValueException`, which is included with Configula for
exactly this use-case.

You can use your custom `AppConfig` class as follows:

```php
use Configula\ConfigFactory;
$config = AppConfig::fromConfigValues(ConfigFactory::loadPath('/some/path'));

$config->getEncryptionKey();
$config->isDevMode();
// etc...
```

## Using Symfony Config to define a configuration schema

In some cases, you may wish to strictly control what configuration values are allowed and also validate those values
when loading the configuration. The [Symfony Config Component](http://symfony.com/doc/current/components/config.html) 
provides an excellent mechanism for accomplishing this.

First, include the Symfony Config Component library in your application:

```
composer require symfony/config
```

Then, create a class that provides your configuration tree.  Refer to the [Symfony Docs](http://symfony.com/doc/current/components/config/definition.html#defining-a-hierarchy-of-configuration-values-using-the-treebuilder)
for all the cool stuff you can do in your configuration tree:

```php

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ConfigTree implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('config');
        
        $rootNode->children()
            ->boolean('devmode')->defaultValue(false)->end()
            ->scalarNode('encryption_key')->isRequired()->cannotBeEmpty()->end()
            ->arrayNode('db')
                ->children()
                    ->scalarNode('host')->cannotBeEmpty()->defaultValue('localhost')->end()
                    ->integerNode('port')->min(0)->defaultValue(3306)->end()
                    ->scalarNode('driver')->cannotBeEmpty()->defaultValue('mysql')->end()
                    ->scalarNode('dbname')->cannotBeEmpty()->end()
                    ->scalarNode('user')->cannotBeEmpty()->end()
                    ->scalarNode('password')->end()
                ->end()
            ->end() // End DB
        -end();
        
        return $treeBuidler;
    }
}

```

Load your configuration as you normally would, and then pass the resulting `ConfigValues` object through
the Symfony filter:

```php

use Configula\ConfigFactory;
use Configula\Util\SymfonyConfigFilter;

// Setup your config tree, and load your configuration
$configTree = new ConfigTree();
$config = ConfigFactory::loadPath('/path/to/config');

// Validate the configuration by filtering it through the allowed values
// If anything goes wrong here, a Symfony exception will be thrown (not a Configula exception)
SymfonyConfigFilter::filter($configTree, $config);
```

## Writing your own loader

In addition to using the built-in loaders, you can write your own loader.  There are two ways to do this:

Extend the `Configula\Loader\AbstractFileLoader` to write your own loader that reads data from a file.

```php

use Configula\Loader\AbstractFileLoader;

class MyFileLoader extends AbstractFileLoader
{
        /**
         * Parse file contents
         *
         * @param string $rawFileContents
         * @return array
         */
        abstract protected function parse(string $rawFileContents): array
        {
            // Parse the file contents and return an array of values.
        }
}

```

Use it:

```php

use Configula\ConfigFactory;

// use the factory..
$config = ConfigFactory->load(new MyFileLoader('/path/to/file'));

// ..or don't..
$config = (new MyFileLoader('/path/to/file'))->load();
```

Create your own implementation of `Configula\Loader\ConfigLoaderInterface`, and you can load configuration from anywhere:

```php

use Configula\Loader\ConfigLoaderInterface;
use Configula\Exception\ConfigLoaderException;
use Configula\ConfigValues;

class MyLoader implements ConfigLoaderInterface
{
    public function load(): ConfigValues
    {
        if (! $arrayOfValues = doWorkToLoadValuesHere()) {
            throw new ConfigLoaderException("Something went wrong..");
        }
        
        return new ConfigValues($arrayOfValues);
    }
}

```
Use it:

```php

use Configula\ConfigFactory;

// use the factory..
$config = ConfigFactory->load(new MyLoader('/path/to/file'));

// ..or don't..
$config = (new MyLoader('/path/to/file'))->load();
```