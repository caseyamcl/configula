Version 3.0 (Major Rewrite)

## Added

* Loader (add details here)
  * All configuration loading has been moved into separate classes
  * Invalid files or other load errors now throw a `ConfigLoaderException`
* Created `get()` method as shortcut to `getValue()`
* Validator and Symfony Configuration Validator Bridge
* `Config::build()` command to replace old constructor behavior

## Changed

* Switched from PSR-0 to PSR-4
* Constructor now accepts an array of values instead of a file path
* `Config::getValue()` object now throws exception if invalid configuration value desired and no default value specified
* Drivers 
    * Drivers no longer have to be named after the file extension they support

## Removed
