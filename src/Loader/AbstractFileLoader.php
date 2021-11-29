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
use Configula\Exception\ConfigFileNotFoundException;
use Configula\Exception\ConfigLoaderException;

/**
 * Class AbstractFileLoader
 *
 * @package Configula\Loader
 */
abstract class AbstractFileLoader implements FileLoaderInterface
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @var bool
     */
    private $required;

    /**
     * AbstractFileLoader constructor.
     *
     * @param string $filePath
     * @param bool   $required If TRUE, this file is required to exist
     */
    public function __construct(string $filePath, bool $required = true)
    {
        $this->filePath = $filePath;
        $this->required = $required;
    }

    /**
     * Load config
     *
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        if (! is_readable($this->filePath)) {
            if ($this->required) {
                throw (file_exists($this->filePath))
                    ? new ConfigLoaderException("Could not read configuration file: " . $this->filePath)
                    : new ConfigFileNotFoundException('Config file not found: ' . $this->filePath);
            } else {
                return new ConfigValues([]);
            }
        }

        $values = $this->parse(file_get_contents($this->filePath));
        return new ConfigValues($values);
    }

    /**
     * Parse the contents
     *
     * @param  string $rawFileContents
     * @return array
     */
    abstract protected function parse(string $rawFileContents): array;

    /**
     * @return string
     */
    protected function getFilePath(): string
    {
        return $this->filePath;
    }
}
