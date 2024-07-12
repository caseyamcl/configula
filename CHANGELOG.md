# Changelog

All notable changes to `Configula` will be documented in this file since v3.0
Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [3.2.0] (2024-07-12)
### Added
- GitHub builds for v3
- Support for `dflydev/dot-access-data` v3
### Fixed
- Incorrect information in `README.md`
- Adhere to PSR-12 coding standards 
### Removed
- Old defunct `.scrutinizer.yml` and `.travis.yml` configuration files 

## [3.1.0] (2020-01-14)
### Added
- PHP 7.4 test in `travis.ci`
- Support for Symfony v5
- `.editorconfig` file (to make developers' lives easier :)
- Strict types declaration on every file (`declare(strict_types=1);`)
### Changed
- Update to PSR-12 coding standard (from PSR-2)
### Fixed
- Explicitly cast results to string in order to facilitate strict types

## [3.0.0] (2019-04-11)
### Added
- Added this changelog
- Added `ConfigValues::__invoke()` method
- Added `ConfigValues::get()` method
- Added `ConfigValues::hasValue()` method
- Added `ConfigValues::find()` method
- Added `ConfigValues::has()` method
- Added `ConfigValues::getIterator()` method
- Added `ConfigValues::fromConfigValues()` method
- Added `ConfigValues::getArrayCopy()` method
- Added Validator and Symfony Configuration Validator Bridge
- Added `ConfigFactory` to replace v2 functionality and add additional utility methods
- Added `ConfigLoaderInterface` and all loaders in `Loader` namespace to replace drivers
- Added ability to load values from environment and any arbitrary source (via `ConfigLoaderInterface`)
- Added filters, including `SymfonyConfigFilter`
### Changed
- Main config class changed from `Config` to `ConfigValues`
- Configula now requires PHP v7.1 or newer.  Use Configula v2.0 for PHP5.x/7.0 support.
- Configula now requires Symfony 3.4 or newer.
- All configuration loading has been moved from `ConfigValues` constructor into separate classes.
- Invalid files or other load errors now throw a `ConfigLoaderException`
- Switched from PSR-0 to PSR-4
- `ConfigValues` constructor now accepts an array of values instead of a file path
- `ConfigValues` now implements `IteratorAggregate` instead of `Iterator`
- Use `dfyldev/data` for access to nested values via dot-notation (removed `getNestedVar` method)
### Removed
- Removed `Config(Values)::loadConfig()`.  Use loaders now.
- Removed `Config(Values)::loadConfgFile()`.  Use loaders now.
- Removed `Config(Values)::parseConfigFile()`.
- Removed `DriverInterface` and all drivers.  Use loaders now.
- Removed `ConfigulaException`.  All exceptions now extend `Exception\ConfigException`.
### Deprecated
- `ConfigValues::getItems()` is now deprecated.  Use `ConfigValues::getArrayCopy()` instead.
- `ConfigValues::getItem()` is now deprecated.  Use `ConfigValues::get()` instead.
- `ConfigValues::valid()` is now deprecated.  Use `ConfigValues::has()` instead.
