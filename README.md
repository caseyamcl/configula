Configula
=========

Configula is a simple configuration library for PHP 5.3+.  

Use it when you don't need all the power and flexibility of the  Symfony2 Configuration library, but instead just need a simple class to read configuration files of different types.

[![Build Status](https://travis-ci.org/caseyamcl/Configula.png?branch=master)](https://travis-ci.org/caseyamcl/Configula)

Features
--------
* Works with _.php_, _.ini_, _.json_, and _.yml_ configuration file types
* Easily write a plugin to support other filetypes
* Simple usage:

        //Access configuration values
        $config = new Configula\Config('/path/to/config/files');
        $some_value = $config->getValue('some_key');
        
* Property-like access to your config settings:

        //Access configuration values
        $config = new Configula\Config('/path/to/config/files');
        $some_value = $config->some_key;

* Array and iterator access to your config settings:

        //Access conifguration values
        $config = new Configula\Config('/path/to/config/files');
        foreach ($config as $item => $value) {
            echo "<li>{$item} is {$value}</li>";
        }

* [Packagist/Composer](http://getcomposer.org) and [PSR-0 Compliant](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md, "PSR-0 Standards Explanation")
* Unit-Tested!  Just about 100% coverage.


Installation
------------

###Installation via source:

1. Download from [Github](http://github.com/caseyamcl/Configula, "Github Page for Configula")
2. Drop the _src/Configula_ folder into your codebase (you don't need the parent folders)
3. Use a [PSR-0 Autoloader](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md, "PSR-0 Standards Explanation") to include it!

###Installation via Composer (Packagist):

1. Include the following in your _composer.json_ file:

        "require": {
            ...
            "caseyamcl/Configula": "~2.2"
        }

2. Then, run <code>php composer.phar install</code>

Basic Usage
-----------

1.  Create a single folder in your application for storing configuration files.
2.  Populate the folder with configuration files.  See _Config Folder Layout_ section below for more details.
3.  Instantiate a configula instance, and send the path as the first parameter:

        $config = new Configula\Config('/path/to/app/config');

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


Config Folder Layout
--------------------

You can use any single folder to store configuration files.  You can also mix and match any supported configuration filetypes.  Current supported filetypes are:

* __PHP__ - Configula will look for an array called <code>$config</code> in this file.
* __JSON__ - Uses the built-in PHP <code>json_decode()</code> function
* __YAML__ - YAML parsing depends on the symfony/yaml package (v2.1.0 or higher)
* __INI__ - Uses the built-in PHP <code>parse_ini_file()</code> function

### Local Configuration Files

In some cases, you may want to have local configuration files that override the default configuration files.  To override any configuration file, create another configuration file, and append <code>.local.EXT</code> to the end.

For example, a configuration file named <code>database.yml</code> is overridden by <code>database.local.yml</code>, if the latter file exists.

This is very useful if you want certain settings included in version control, and certain settings ignored (just add <code>/path/to/config/*.local.EXT</code> to your <code>.gitignore</code> or equivalent VCS file)


Writing Your Own Configuration File Type Driver
-----------------------------------------------

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

Todo
----
* Implement Exceptions for invalid configuration files
