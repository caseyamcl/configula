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
 * Class IniFileLoader
 *
 * @package Configula\Loader
 */
final class IniFileLoader implements FileLoaderInterface
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @var bool
     */
    private $processSections;

    /**
     * IniFileLoader constructor.
     *
     * @param string $filePath
     * @param bool   $processSections
     */
    public function __construct(string $filePath, bool $processSections = true)
    {
        $this->filePath = $filePath;
        $this->processSections = $processSections;
    }

    /**
     * Load config
     *
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        try {
            $values = parse_ini_file($this->filePath, $this->processSections, INI_SCANNER_TYPED) ?: [];
        } catch (Throwable $e) {
            throw new ConfigLoaderException("Error parsing INI file: " . $this->filePath);
        }

        return new ConfigValues($values);
    }
}
