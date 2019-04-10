# Changelog

All notable changes to `Configula` will be documented in this file since v3.0
Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [3.0.0] (unreleased)
### Added
- Added this changelog
- Added `__invoke()` method
- Added `find()` method
- Added Validator and Symfony Configuration Validator Bridge
- Added `ConfigFactory` to replace v2 functionality and add additional utility methods
- Added `COnfigValues`
### Changed
- Main config class changed from `Config` to `ConfigValues`
- Configula now requires PHP v7.1 or newer.  Use v2.0 for PHP5.x/7.0 support
- All configuration loading has been moved into separate classes.
- Invalid files or other load errors now throw a `ConfigLoaderException`
- Switched from PSR-0 to PSR-4
- `ConfigValues` constructor now accepts an array of values instead of a file path
- `Config::getValue()` object now throws exception if invalid configuration value desired and no default value specified
- Drivers no longer have to be named after the file extension they support
- `ConfigValues` now implements `IteratorAggregate` instead of `Iterator`
### Removed
- Removed `Config(Values)::loadConfig()`.  Use loaders now.
- Removed `Config(Values)::loadConfgFile()`.  Use loaders now.
- Removed `Config(Values)::parseConfigFile()`.
### Deprecated
- `ConfigValues::getItems()` is now deprecated.  Use `ConfigValues::getArrayCopy()` instead.
- `ConfigValues::getItem()` is now deprecated.  Use `ConfigValues::get()` instead.
- `ConfigValues::valid()` is now deprecated.  Use `ConfigValues::has()` instead.