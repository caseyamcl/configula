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
    * Array-access (read-only)
    * `get(val)` and `has(val)` methods
    * Magic methods (`__get(val)`, `__isset(val)`, and `__invoke(val)`)
    * Implements `Traversable` and `Countable` interfaces
* Provides simple dot-based access to nested values (e.g. `$config->get('application.sitename.prefix');`)
* Code quality standards: PSR-2, 100% Unit Test Coverage

## Installation

```bash
composer require caseyamcl/configula
```

## Need PHP v5.* compatibility?

Configula Version 2.x is compatible with PHP v5.3+.  If you need PHP 5.x compatibility, instruct Composer to use
the **2.x** version of this library instead of the current one:

```
composer require caseyamcl/configula:^2.4
```

## Upgrading?

Refer to [UPGRADE.md](UPGRADE.md) for notes on upgrading from Version 2.x to Version 3.
 
## Loading Configuration
  
You can use the `Configula\ConfigFactory` to load configuration from files, the environment or other sources: 

```php
use Configula\ConfigFactory as Config;

// Load all .yml, .php, .json, and .ini files from directory (recursive)
// Supports '.local' and '.dist' modifiers to load config in correct order
$config = Config::loadPath('/path/to/config/files', ['optional' => 'defaults', ...]);

// Load all .yml, .php, .json, and .in files from directory (non-recursive)
// Supports '.local' and '.dist' modifiers to load config in correct order
$config = Config::loadSingleDirectory('/path/to/config/files', ['optional' => 'defaults', ...]);

// Load from array
$config = Config::loadFromArray(['some' => 'values']);

// Chain loaders -- performs deep merge
$config = Config::loadFromArray(['some' => 'values'])
    ->merge(Config::loadPath('/some/path'))
    ->merge(Config::loadEnv('MY_APP'));
```

Or, if you are loading an array, you can instantiate `Configula\ConfigValues` directly:

```php
$config = new Configula\ConfigValues(['array' => 'values' ...]);
```

Or, you can manually invoke any of the loaders in the `Configula\Laoder` namespace:

```php
$config = (new Configula\Loader\FileListLoader(['file-1.yml', 'file-2.json']))->load();
```


## Accessing Values

The `Configula\ConfigValues` object provides several ways to access your configuration values:

```php
// get method - throws exception if value does not exist
$config->get('some_value');

// find method - returns NULL if value does not exist
$config->find('some_value');
 
// has method - returns TRUE or FALSE
$config->has('some_value');
  
// hasValue method - returns TRUE if value exists and is not empty (NULL, [], "")
$config->hasValue('some_value');   

```
   
### Accessing values using dot notation

Configula supports accessing values via dot-notation (e.g `some.nested.var`):

```php
// Here is a nested array:
$values = [
    'debug' => true,
    'db' => [
        'platform' => 'mysql',
        'credentials' => [
            'username' => 'some',
            'password' => 'thing'
        ]
    ],
];

// Load it into Configula
$config = new \Configula\ConfigValues($values);

// Access top-level item
$values->get('debug'); // bool; TRUE

// Access nested item
$values->get('db.platform'); // string; 'mysql'

// Access deeply nested item
$values->get('db.credentials.username'); // string: 'some'

// Get item as array
$values->get('db'); // array with sub-values

// has/hasValue work too
$values->has('db.credentials.key'); // false
$values->hasValue('db.credentials.key); // false
```
        
Property-like access to your config settings:

```php
// Access configuration values
$config = Config::loadPath('/path/to/config/files');

// Throws exception if value does not exist
$some_value = $config->some_key;

// Returns TRUE or FALSE
isset($config->some_key);
```

Iterator access to your config settings:

```php
// Basic iteration
foreach ($config as $item => $value) {
    echo "<li>{$item} is {$value}</li>";
}
```

Magic invoke method access to your config settings:

```php
// Throws exception if value does not exist
$value = $config('some_value'); 

// Returns default value if value does not exist
$value = $config('some_value', 'default');
```

Array access to your config settings:

```php
// Throws exception if value does not exist
$some_value = $config['some_key'];    

// Returns TRUE or FALSE
$exists = isset($config['some_key']); 

// Not allowed; always throws exception (config is immutable)
$config['some_key'] = 'foobar'; 
unset($config['some_key']);   
```

## Merging Configuration

Since `Configula\ConfigValues` is an immutable object, you cannot mutate the configuration
once it is set.  However, you can merge values and get a new copy of the object using the `merge`
or `mergeValues` methods:

```php
use Configula\ConfigValues;

$config = new ConfigValues(['foo' => 'bar, 'baz' => 'biz');

// Merge configuration using merge()
$newConfig = $config->merge(new ConfigValues(['baz' => 'buzz', 'cad' => 'cuzz]));

// For convenience, you can pass in an array using mergeValues()
$newConfig = $config->merge(['baz' => 'buzz', 'cad' => ['some' => 'thing']]);
```

Configula performs a *deep merge*.  Nested arrays are traversed and the last value always takes precedence.

Note that Configula does not deep merge nested objects, only arrays.

## Iterator

The built-in `ConfigValues::getIterator()` method flattens nested values when iterating:

```php
// Here is a nested array
$config = new Configula\ConfigValues([
    'debug' => true,
    'db' => [
        'platform' => 'mysql',
        'credentials' => [
            'username' => 'some',
            'password' => 'thing'
        ]
    ],
]);

foreach ($config as $path => $value) {
    echo "\n" . $path . ": " . $value;
}

// Output:
//
// debug: 1
// db.platform: mysql
// db.credentials.username: some
// db.credentials.password: thing
// 

```

If you want to iterate only the top-level items in your configuration, you can use the `getArrayCopy()` method:

```php
foreach ($config->getArrayCopy() as $path => $value) {
    echo "\n" . $path . ": " . $value;
}

// Output:
//
// debug: 1
// db: Array
//

```

## Using the Folder Loader - Config Folder Layout

By default, the FileLoader, FileListLoader, and FolderLoader in Configula will load files with the following extensions:

* `php` - Configula will look for an array called `$config` in this file.
* `json` - Uses the built-in PHP `json_decode()` function
* `yaml` or `yml` - Uses the [Symfony YAML parser](https://symfony.com/doc/current/components/yaml.html)
* `ini` - Uses the built-in PHP `parse_ini_file()` function

The `ConfigFactory::loadPath()` loader will traverse directories in your configuration path recursively.

The `ConfigFactory::loadSingleDirectory()` will load your configuration in a single directory non-recursively.

### Local Configuration Files

In some cases, you may want to have local configuration files that override the default configuration files.

There are two ways to do this:

1. prefix the default configuration file extension with `.dist`(e.g. `config.dist.yml`), and name the local
   configuration file normally: `config.yml`
2. name the default configuration file normally (e.g. `config.yml`) and prefix `.local` to the extension for the local
   configuration file: `config.local.yml`.

Either way will work, and you could even combine approaches if you want.  The file iterator will always cascade merge 
files in this order:

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

## Loading from environment variables

There are two common ways that configuration is generally stored in environment:

1. As multiple environment variables (perhaps loaded by phpDotEnv or Symfony dotEnv, or exposed by Heroku/Kubernetes/etc.).
2. As a single environment variable with a JSON-encoded value, which exposes the entire configuration tree.

Configula supports both.  You can also write your own loader if your environment is different.

### Loading multiple environment variables

Configula supports loading environment variables as configuration values using `getenv()`.  This is the 
[12 Factor App](https://12factor.net/config) way of doing things.

Common use-cases for this loader include:

1. Loading system environment as config values
2. Loading values using [phpDotEnv](https://github.com/vlucas/phpdotenv) or [Symfony dotEnv](https://symfony.com/doc/current/components/dotenv.html)
3. Accessing values injected into the environment by a cloud provider (Kubernetes, Docker, Heroku, etc.)

By default, this loader will transform your environment variable names as follows:

1. Underscores ("_") will be converted into dots (".")
2. Characters will be converted to lower-case

The default behavior is to load the configuration values directly:

```php
$config = ConfigFactory::loadEnv();
```

Result:

```
MYAPP_MYSQL_USERNAME="..."   --> becomes --> $config->get('MYAPP_MYSQL_USERNAME')
MYAPP_MYSQL_PASSWORD="..."   --> becomes --> $config->get('MYAPP_MYSQL_PASSWORD')
MYAPP_MYSQL_HOST_PORT="..."  --> becomes --> $config->get('MYAPP_MYSQL_HOST_PORT')
MYAPP_MYSQL_HOST_NAME="..."  --> becomes --> $config->get('MYAPP_MYSQL_HOST_NAME')
SERVER_NAME="..."            --> becomes --> $config->get('SERVER_NAME')
etc..
```

You can load *ONLY* environment variables with a specific prefix.  Configula will remove the prefix
when loading the configuration:

```php
$config = ConfigFactory::loadEnv('MYAPP_');
```

Result:

```
MYAPP_MYSQL_USERNAME="..."   --> becomes --> $config->get('MYSQL_USERNAME')
MYAPP_MYSQL_PASSWORD="..."   --> becomes --> $config->get('MYSQL_PASSWORD')
MYAPP_MYSQL_HOST_PORT="..."  --> becomes --> $config->get('MYSQL_HOST_PORT')
MYAPP_MYSQL_HOST_NAME="..."  --> becomes --> $config->get('MYSQL_HOST_NAME')
SERVER_NAME="..."            --> ignored
etc..
```

You can convert a flat list into nested config values by passing a delimiter to break on:  

```php
$config = ConfigFactory::loadEnv('MYAPP_', '_');
```

Result:

```
MYAPP_MYSQL_USERNAME="..."   --> becomes --> $config->get('MYSQL.USERNAME')
MYAPP_MYSQL_PASSWORD="..."   --> becomes --> $config->get('MYSQL.PASSWORD')
MYAPP_MYSQL_HOST_PORT="..."  --> becomes --> $config->get('MYSQL.HOST.PORT')
MYAPP_MYSQL_HOST_NAME="..."  --> becomes --> $config->get('MYSQL.HOST.NAME')
```

You can transform all of the values to lower-case by passing TRUE as the last argument:

```php
$config = ConfigFactory::loadEnv('MYAPP_', '_', true);
```

Result:

```
MYAPP_MYSQL_USERNAME="..."   --> becomes --> $config->get('mysql.username')
MYAPP_MYSQL_PASSWORD="..."   --> becomes --> $config->get('mysql.password')
MYAPP_MYSQL_HOST_PORT="..."  --> becomes --> $config->get('mysql.host.port')
MYAPP_MYSQL_HOST_NAME="..."  --> becomes --> $config->get('mysql.host.name')
```

Instead of using a prefix, you can pass a regex string to limit returned values:

```php
$config = ConfigFactory::LoadEnvRegex('/.+_MYAPP_.+/', '_', true);
```

Result:

```
MYAPP_MYSQL_USERNAME="..."   --> becomes --> $config->get('myapp.mysql.username')
MYAPP_MYSQL_PASSWORD="..."   --> becomes --> $config->get('myapp.mysql.password')
MYAPP_MYSQL_HOST_PORT="..."  --> becomes --> $config->get('myapp.mysql.host.port')
EMAIL_MYAPP_SERVER="..."     --> becomes --> $config->get('email.myapp.server')
SERVER_NAME="..."            --> ignored
```


### Loading a single JSON-encoded environment variable

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

## Loading from multiple loaders

You can use the `Configula\ConfigFactory::loadMultiple()` to load from multiple sources and merge results.
This method accepts an iterator where each value is one of the following:

* Instance of `Configula\ConfigLoader\ConfigLoaderInterface`
* Array of configuration values
* String or instance of `SplFileInfo` that is a path to a file or directory

Any other value in the iterator will trigger an `\InvalidArgument` exception

```php
use Configula\ConfigFactory as Config;
use Configula\Loader;

$config = Config::loadMultiple([
    new Loader\EnvLoader('My_APP'),
    ['some' => 'values'],
    '/path/to/some/file.yml',
    new \SplFileInfo('/path/to/another/file.json')
]);

Alternatively, you can pass an iterator of `Configula\ConfigLoaderInterface` instances to
`Configula\Loader\CascadingConfigLoader`.
```

## Handling Errors

All exceptions extend `Configula\Exception\ConfigException`.  You can catch this exception to catch all errors thrown 
during loading and reading of configuration values.

### Loading Exceptions:

* `ConfigLoaderException` is thrown when an error occurs during loading configuration.
* `ConfigFileNotFoundException` is thrown when a required configuration file or path is missing.  It is generally
  thrown from the `FileLoader` loader when the `$required` constructor parameter is set to `true`. 
* `UnmappedFileExtensionException` is thrown from the `DecidingFileLoader` for files with unrecognized extensions.

### Config Value Exceptions:
 
* `ConfigValueNotFoundException` is thrown when trying to reference a non-existent configuration value name and
  no default value is specified.
* `ConfigLogicException` is thrown when attempting to mutate configuration via array
* `InvalidConfigValueException` is not thrown from any Configula class directly, but provided as an option for
  implementing libraries (see "_Extending the `ConfigValues` class_" below).

```php
// These throw a ConfigValueNotFoundException
$config->get('non_existent_value');
$config['non_existent_value'];
$config->non_existent_value;

// This will not throw an exception, but instead return NULL
$config->find('non_existent_value');

// This will not throw an exception, but instead return 'default'
$config->get('non_existent_value', 'default');
```

## Extending the `ConfigValues` class to make accessing configuration easy

While it is entirely possible to use the `Configula\ConfigValues` class directly , you might also want to provide 
some application-specific methods to load configuration values.  This creates a better developer experience. 

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

// ..or use it directly.
$config = (new MyLoader())->load();
```