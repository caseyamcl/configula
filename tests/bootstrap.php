<?php

/**
 * Configula Unit Tests Bootstrap File
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 * @license MIT
 * @package Configula
 * @subpackage Unit Tests
 */

//Files to ensure exist
$checkFiles['autoload'] = __DIR__.'/../vendor/autoload.php';
$checkFiles[] = __DIR__.'/../vendor/symfony/yaml/Symfony/Component/Yaml/Yaml.php';

//Check 'Em
foreach($checkFiles as $file) {

    if ( ! file_exists($file)) {
        throw new RuntimeException('Install development dependencies to run test suite.');
    }
}

//Away we go
$autoload = require_once $checkFiles['autoload'];

/* EOF: bootstrap.php */