<?php

/**
 * Configula Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/configula
 * @version 4
 * @package caseyamcl/configula
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 *
 * For the full copyright and license information, - please view the LICENSE.md
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

declare(strict_types=1);

namespace Configula\Loader;

use Configula\ConfigValues;
use Configula\Exception\ConfigLoaderException;
use Throwable;

/**
 * Class PhpFileLoader
 *
 * @package Configula\Loader
 */
final class PhpFileLoader implements FileLoaderInterface
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * IniFileLoader constructor.
     *
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Load config
     *
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        // If file is not readable for any reason (permissions, etc), throw an exception
        if (! is_readable($this->filePath)) {
            throw new ConfigLoaderException("Error reading config from file (check permissions?): " . $this->filePath);
        }

        // If file is empty, just return empty ConfigValues object
        if (trim(file_get_contents($this->filePath)) === "") {
            return new ConfigValues([]);
        }

        try {
            $config = null;

            // Loading the file will either overwrite the $config variable or the file itself will return an array
            ob_start();
            $configFromReturn = include $this->filePath;
            ob_end_clean();

            /** @phpstan-ignore-next-line Ignore because we are doing some things that PHPStan doesn't understand */
            if (!is_array($config)) {
                $config = $configFromReturn;
            }

            // If the config file still isn't an array, throw an exception
            if (!is_array($config)) {
                throw new ConfigLoaderException("Missing or invalid \$config array in file: " . $this->filePath);
            }
            return new ConfigValues($config);
        } catch (Throwable $e) {
            throw new ConfigLoaderException(
                "Error loading configuration from file: " . $this->filePath,
                $e->getCode(),
                $e
            );
        }
    }
}
