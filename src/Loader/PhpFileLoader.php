<?php

/**
 * Configula Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/configula
 * @version 3.0
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
        // If file is empty, just return empty ConfigValues object
        if (trim(file_get_contents($this->filePath)) === "") {
            return new ConfigValues([]);
        }

        // If file is not readable for any reason (permissions, etc), throw an exception
        if (! is_readable($this->filePath)) {
            throw new ConfigLoaderException("Error reading config from file (check permissions?): " . $this->filePath);
        }

        try {
            $config = null;

            ob_start();
            /** @noinspection PhpIncludeInspection */
            include $this->filePath;
            ob_end_clean();

            // If the config file isn't an array, throw an exception
            /** @phpstan-ignore-next-line Ignore because we are doing some things that PHPStan doesn't understand */
            if (!is_array($config)) {
                throw new ConfigLoaderException("Missing or invalid \$config array in file: " . $this->filePath);
            }
            /** @phpstan-ignore-next-line Ignore because we are doing some things that PHPStan doesn't understand */
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
