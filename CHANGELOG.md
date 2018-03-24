# Changelog

All notable changes to `Configula` will be documented in this file since v3.0
Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [3.0.0] (unreleased)
### Added
- Created `get()` method as shortcut to `getValue()`
- Added `__invoke()` method
- Added `find()` method
- Added Validator and Symfony Configuration Validator Bridge
- `Config::build()` command to replace old constructor behavior
- Added this changelog
- Added `ConfigFactory` to replace v2 functionality and add additional utility methods
### Changed
- All configuration loading has been moved into separate classes
- Invalid files or other load errors now throw a `ConfigLoaderException`
- Configula now required PHP v7.1 or newer.  Use v2.0 for PHP5.x support
- Switched from PSR-0 to PSR-4
- Constructor now accepts an array of values instead of a file path
- `Config::getValue()` object now throws exception if invalid configuration value desired and no default value specified
- Drivers no longer have to be named after the file extension they support
### Deprecated
- `ConfigValues::getItems()` is now deprecated.  Use `ConfigValues::getArrayCopy()` instead