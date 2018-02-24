# Changelog

All notable changes to `saluki` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [3.0.0] (unreleased)
### Added
- Separated loading 
  - All configuration loading has been moved into separate classes
  - Invalid files or other load errors now throw a `ConfigLoaderException`
- Created `get()` method as shortcut to `getValue()`.
- Validator and Symfony Configuration Validator Bridge
- `Config::build()` command to replace old constructor behavior
- Added this changelog
### Changed
- Configula now required PHP v7.1 or newer.  Use v2.0 for PHP5.x support
- Switched from PSR-0 to PSR-4
- Constructor now accepts an array of values instead of a file path
- `Config::getValue()` object now throws exception if invalid configuration value desired and no default value specified
- Drivers no longer have to be named after the file extension they support
### Deprecated
- `ConfigValues::getItems()` is now deprecated.  Use `ConfigValues::getArrayCopy()` instead