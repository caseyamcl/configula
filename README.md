# Configula

Configula is a simple configuration library for PHP 7.1+.  

Use this library when you want a simple tool for loading and providing configuration values in your application code.

You can use it with the [Symfony Config Component](http://symfony.com/doc/current/components/config/introduction.html),
or as a standalone tool.

[![Build Status](https://travis-ci.org/caseyamcl/Configula.png?branch=master)](https://travis-ci.org/caseyamcl/Configula)

## Features

* Load configuration from a variety of sources:
    * Load values from _.php_, _.ini_, _.json_, and _.yml_ configuration file types
    * Load values from the environment
    * Easily write your own extensions to support other filetypes and sources
* Multiple load strategies:
    * Cascade-load values from multiple sources (default)
    * Load configuration from first found source
* Provide hard-coded default values for some or all configuration values
* Optionally use along with [Symfony Config Component](http://symfony.com/doc/current/components/config/introduction.html)
  to validate configuration values and/or cache them
* Provides an immutable object to access your configuration values in your application:
    * Array-access
    * Standard value `get(val)` access
    * Magic method `__get(val)` access
    * Implements `Iterator` and `Countable` interfaces
* Provides simple dot-based access to nested values (e.g. `$config->get('application.sitename.prefix');`)
* Code quality standards: PSR-2, 100% Unit Test Coverage
  
## Need PHP v5.* compatibility?

The old version of Configula was compatible with PHP 5.3+.  If you prefer to use that
version, simply instruct use Composer to use the **2.x** version:

```
composer require caseyamcl/configula:~2.4
```

## Upgrading?

Refer to `UPGRADE.md` for notes on upgrading from Version 2.x to Version 3.
 
## Quick Start
  
* Simple usage:

        //Access configuration values
        $config = Configula\Config::load('/path/to/config/files');
        $some_value = $config->get('some_key');
        
* Property-like access to your config settings:

        //Access configuration values
        $config = Configula\Config::load('/path/to/config/files');
        $some_value = $config->some_key;

* Array and iterator access to your config settings:

        //Access conifguration values
        $config = Configula\Config::load('/path/to/config/files');
        foreach ($config as $item => $value) {
            echo "<li>{$item} is {$value}</li>";
        }

## Installation

### Installation via Composer:

```
composer require caseyamcl/configula
```

### Non-composer installation

This is not recommended, but it is possible.  

1. Add the `src/` directory to your application (`cp -r src/* /PATH/TO/YOUR/APP/configula`)
2. Either use `require` statements for ALL classes, or use a [PSR-4 compatible autoloader](http://www.php-fig.org/psr/psr-4/)
   like [this one](https://packagist.org/packages/keradus/psr4autoloader).

## Basic Usage

1.  Create a single folder in your application for storing configuration files.
2.  Populate the folder with configuration files.  See [Config Folder Layout](#config-folder-layout) section below for more details.
3.  Instantiate a Configula instance, and pass the path as the first parameter:

        $config = Configula\Config::load('/path/to/app/config');

4.  Configuration values become properties of the Configula object:

        $my_value = $config->my_config_setting;

5.  Alternatively, use the <code>getItem()</code> method, which accepts an optional default value:

        $my_value = $config->getItem('my_config_setting', 'default_to_fall_back_on');

6.  Finally, you can access the object as if it were an array:

        $my_value = $config['my_config_setting'];

7.  foreach() and count() also work, since Configula implements those interfaces:

        foreach ($config as $settingName => $settingValue) {
            echo "{$settingName} is {$settingValue}";
        }
        echo "There are " . count($config) . " settings total";

8.  To get all config settings as an array, use the <code>getItems()</code> method:

        $all_values = $config->getItems();

9.  If you would like to preload the config object with default values, send those as the second parameter upon instantiation:

        //Values in the config files will override the default values
        $defaults = array('foo' => 'bar', 'baz' => 'biz');
        $config = new Configula\Config('/path/to/app/config', $defaults);

10. If you would like to use Configula with only default values, do not provide a path to the configuration directory:

        //The default values will be the only config options available
        $defaults = array('foo' => 'bar', 'baz' => 'biz');
        $config = new Configula\Config(null, $defaults);

Notes:

* The Config object, once instantiated, is immutable, meaning that it is read-only.  You can not alter the config values.  You can, however, create as many Configula objects as you would like. 
* If any configuration file contains invalid code (invalid PHP or malformed JSON, for example), the Configula class will not throw an error.  Instead, it will simply skip reading that file.


## Config Folder Layout

You can use any single folder to store configuration files.  You can also mix and match any supported configuration filetypes.  Current supported filetypes are:

* __PHP__ - Configula will look for an array called <code>$config</code> in this file.
* __JSON__ - Uses the built-in PHP <code>json_decode()</code> function
* __YAML__ - YAML parsing depends on the symfony/yaml package (v2.1.0 or higher)
* __INI__ - Uses the built-in PHP <code>parse_ini_file()</code> function

### Local Configuration Files

In some cases, you may want to have local configuration files that override the default configuration files.  To override any configuration file, create another configuration file, and append <code>.local.EXT</code> to the end.

For example, a configuration file named <code>database.yml</code> is overridden by <code>database.local.yml</code>, if the latter file exists.

This is very useful if you want certain settings included in version control, and certain settings ignored (just add <code>/path/to/config/*.local.EXT</code> to your <code>.gitignore</code> or equivalent VCS file)


## Writing Your Own Configuration File Type Driver

In addition to the built-in filetype drivers, you can add your own driver for reading configuration files.  An example would look like

    namespace Configula\Drivers;
    use Configula\DriverInterface;

    class MyDriver implements DriverInterface
    {
        /**
         * Your read() method should accept a filepath string
         * and return an array.  Return an empty array if the
         * file is unreadable or unparsable for any reason.
         *
         * @param string $filepath  The realpath to the config file
         * @return array            Empty if non-parsed
         */
        public function read($filepath)
        {
            $contents = file_get_contents($filepath);
            return $this->parse($contents) ?: array();
        }

        /**
         * Example parse method
         *
         * @param string  Raw file contents
         * @return array  Parsed configuration as associatve array
         */
        protected function parse($contents)
        {
            /* ... code here .... */
            return $result;
        }

    }

Refer to an existing Unit test for an example of how to test your driver class.

