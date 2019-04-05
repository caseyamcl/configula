# Configula

Configula is a configuration library for PHP 7.1+. 


[![Build Status](https://travis-ci.org/caseyamcl/Configula.png?branch=master)](https://travis-ci.org/caseyamcl/Configula)

Use this library when you want to load configuration from the filesystem, environment, and other sources.  It implements
your configuration values as an immutable object in PHP.  It is a framework-independent tool, and can be easily used in 
any PHP application.

## Features

* Load configuration from a variety of sources:
    * Load values from _.php_, _.ini_, _.json_, and _.yml_ configuration file types
    * Load values from the environment, and _.env_ files using a DotEnv library ([vlucas](https://github.com/vlucas/phpdotenv) or [Symfony](https://github.com/symfony/dotenv))
    * Easily write your own loaders to support other file types and sources
* Cascade/merge values from multiple sources (e.g. array, files, environment, etc) 
* Optionally use in combination with [Symfony Config Component](http://symfony.com/doc/current/components/config/introduction.html)
  to validate configuration values and/or cache them
* Creates an immutable object to access your configuration values in your application:
    * Array-access
    * `get(val)` and `has(val)` methods
    * Magic methods (`__get(val)`, `__isset(val)`, and `__invoke(val)`)
    * Implements `Iterator` and `Countable` interfaces
* Provides simple dot-based access to nested values (e.g. `$config->get('application.sitename.prefix');`)
* Code quality standards: PSR-2, 100% Unit Test Coverage

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
  
## Need PHP v5.* compatibility?

Configula Version 2.x is compatible with PHP v5.3+.  If you need PHP 5.x compatibility, instruct Composer to use
the **2.x** version of this library instead of the current one:

```
composer require caseyamcl/configula:^2.4
```

## Basic usage

1.  Create a folder in your application for storing configuration files.
2.  Populate the folder with configuration files.  See [Config Folder Layout](#config-folder-layout) section 
    below for more details.
3.  Instantiate a Configula instance, and pass the path as the first parameter:

        $config = \Configula\ConfigFactory::loadPath('/path/to/app/config');

4.  Configuration values become properties of the Configula object (if the value does not exist, an exception is thrown):  

        $myValue = $config->my_config_setting;

5.  Alternatively, use the `get()` method, which accepts an optional default value:

        $myValue = $config->get('my_config_setting', 'default_to_fall_back_on');

6. Or, you can use the `find()` method, which returns NULL if the value is not found:

        $myValue = $config->find('my_config_setting'); // Will return NULL if value not set
        
7. Or, you can call the object like a function, which acts like `get()`:

        $myValue = $config('my_config_setting', 'optional_default_value');  

8.  Finally, you can access the object as if it were an array (if the value does not exist, an exception is thrown):

        $myValue = $config['my_config_setting'];

9. Nested values are accessible using dot notation:

        $myValue = $config['dbs.default.host'];
        // or...
        $myValue = $config->get('dbs.default.host');

10.  `foreach()` and `count()` also work, since Configula implements those interfaces:

        foreach ($config as $settingName => $settingValue) {
            echo "{$settingName} is {$settingValue}";
        }
        echo "There are " . count($config) . " settings total";

11.  To get all config settings as an array, use the `getArrayCopy()` method:

        $allValues = $config->getArrayCopy();

12.  If you would like to preload the config object with default values, pass an array as the second parameter to the
    `loadPath` method:

        //Values in the config files will override the default values
        $defaults = ['foo' => 'bar', 'baz' => 'biz'];
        $config = \Configula\ConfigFactory::loadPath('/path/to/app/config', $defaults);

13. If you would like to use Configula with only default values, use the `fromArray` method:

        //The default values will be the only config options available
        $values = array('foo' => 'bar', 'baz' => 'biz');
        $config = \Configula\ConfigFactory::fromArray($values);

### Immutability

The `ConfigValues` object, once instantiated, is immutable, meaning that it is read-only.  You can not alter the config 
values.  You can, however, merge values into a new instance of the object using `ConfigValues::merge()` method.

```php
$values = array('foo' => 'bar', 'baz' => 'biz');
$config = \Configula\ConfigFactory::build($values);

$config = $config->mergeValues(['another' => 'value']);

// Config new contains
// 'foo', 'baz', and 'another' items

// You can also merge any ConfigValues instance 
$config = $config->merge(\Configula\ConfigFactory::path('/some/path'));

```

## Default Behavior

Prior versions of this library were very opinionated about loading files.  This behavior is preserved with the
`ConfigFactory::loadPath()` loader.

### Config Folder Layout

By default, Configula will load files with the following extensions:

* `php` - Configula will look for an array called `$config` in this file.
* `json` - Uses the built-in PHP `json_decode()` function
* `yaml` or `yml` - Uses the [Symfony YAML parser](https://symfony.com/doc/current/components/yaml.html)
* `ini` - Uses the built-in PHP `parse_ini_file()` function

The `ConfigFactory::loadPath()` loader will traverse directories in your configuration path recursively.  If you want
to avoid scanning recursively, see below. 

### Local Configuration Files

In some cases, you may want to have local configuration files that override the default configuration files.  

There are two ways to do this:

1. prefix the default configuration file extension with `.dist`(e.g. `config.dist.yml`), and name the local
   configuration file normally: `config.yml`
2. Name the default configuration file normally (e.g. `config.yml`) and prefix `.local` to the extension for the local
   configuration file: `config.local.yml`.

Either way will work, and you could even combine approaches if you want (not recommended, obviously).  
The file iterator will always cascade merge files in this order:

* `FILENAME.dist.EXT`
* `FILENAME.EXT`
* `FILENAME.local.EXT`

This is useful if you want to keep local configuration files out of revision control.  Choose a paradigm,
and simply add the following to your `.gitignore`

```bash
# If keeping .dist files...
CONFIGDIR/*
!CONFIGDIR/*.dist.*

# or, if ignoring .local files...
CONFIDIR/*.local.*
```

### Example

Consider the following directory layout

```
/my/app/config
 ├config.php
 ├config.dist.php
 └/subfolder
  ├database.yml
  └database.dist.yml	
```

If you use `ConfigFactory::loadPath('/my/app/config')`, the files will be parsed according to their extension and
values will be merged in the following order (values in files later in the list clobber earlier values):

```
- /config.dist.php
- /subfolder/database.dist.yml
- /config.php
- /subfolder/database.yml
```

## Handling Errors

All exceptions extend `Configula\Exception\ConfigException`.  You can catch this exception to catch all possible errors
thrown during loading and reading of configuration values.

* If something goes wrong when loading configuration values, a `Config\Exception\ConfigLoaderException` is thrown.
* If you attempt to read a configuration value that does not exist, and you have not provided a default value at
  runtime, a `Configula\Exception\ConfigValueNotFoundException` is thrown.
* If you try to set config values through the array interface, or pass invalid values to the config loader, a
  `Configula\Exception\ConfigLogicException` is thrown.

```php
// All of these throw a ConfigValueNotFoundException
$config->get('non_existent_value');
$config['non_existent_value'];
$config->non_existent_value;

// This will not throw an exception if the value doesn't exist
$config->find('non_existent_value');

// Nor will this (because a default is provided)
$config->get('non_existent_value', null);
```

## Advanced Loading Options

You can load configuration from any source, using any strategy you wish.  Configula comes with pre-built support
for a few common strategies, or you can create your own combination of loaders to load configuration however you wish.

### Using the `ConfigFactory::loadMultiple()` method



### Loading environment variables

There are two common ways that configuration is generally stored in environment:

1. As multiple environment variables (perhaps loaded by phpDotEnv or Symfony dotEnv, or exposed by Heroku/Kubernetes/etc.).
2. As a single environment variable with a JSON-encoded value, which exposes the entire configuration tree.

Configula supports both.

#### Loading multiple environment variables

Configula supports loading environment variables as configuration values using values in the `$_ENV` array.  This
is the [12 Factor App](https://12factor.net/config) way of doing things.

Common use-cases for this loader include:

1. Loading system environment as config values (note: [`E` must be in your variables-order PHP INI setting](http://php.net/manual/en/reserved.variables.environment.php#98113))
2. Loading values using [phpDotEnv](https://github.com/vlucas/phpdotenv) or [Symfony dotEnv](https://symfony.com/doc/current/components/dotenv.html)
3. Accessing values injected into the environment by a cloud provider (Kubernetes, Docker, Heroku, etc.) 

By default, this loader will transform your environment variable names as follows:

1. Underscores ("_") will be converted into dots (".")
2. Characters will be converted to lower-case

Default Behavior:

```
MYSQL_USERNAME="..."   --> becomes --> $config->get('mysql.username')
MYSQL_PASSWORD="..."   --> becomes --> $config->get('mysql.password')
MYSQL_HOST_PORT="..."  --> becomes --> $config->get('mysql.host.port')
MYSQL_HOST_NAME="..."  --> becomes --> $config->get('mysql.host.name')
```

Usage:

```
use Configula\Loader\EnvLoader;

$configValues = (new EnvLoader())->load();
echo $configValues('mysql.host.port');
```

You can specify a prefix so that Configula will read only environment values that begin with a
specific string.  In this case, the prefix is stripped from the resulting config value name
For example:

```
MYAPP_MYSQL_USER = 'foobar'
MYAPP_MYSQL_HOST = 'localhost'
USER_NAME        = 'bob'
```

```php
use Configula\Loader\EnvLoader;

$configValues = (new EnvLoader('MYAPP_'))->load();
echo $configValues('mysql.user'); // foobar
echo $configValues('mysql.host'); // localhost
echo $configValues('user.name');  // Throws Exception (Config Value)
``` 

If you want to preserve the exact formatting of the environment variable names, instantiate
the `EnvLoader` by passing `null` and `false` as the 2nd and third arguments, respectively:

```php
use Configula\Loader\EnvLoader;


$values = (new EnvLoader('MYAPP_', null, false));
echo $values('MYSQL_USER'); // foobar
``` 

#### Loading a single JSON-encoded environment variable

Use the `JsonEnvLoader` to load a JSON environment variable:

```
MY_ENV_VAR = '{"foo: "bar", "baz": "biz"}'
```

```php
use Configula\Loader\JsonEnvLoader;

$values = (new JsonEnvLoader('MY_ENV_VAR'))->load();

echo $values->foo;
echo $values['bar'];
```

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

// Build it
$config = AppConfig::fromConfigValues(ConfigFactory::loadPath('/some/path'));

// Use it
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
$config = ConfigFactory::load(new MyFileLoader('/path/to/file'));

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
$config = ConfigFactory::load(new MyLoader());

// ..or don't..
$config = (new MyLoader())->load();
```