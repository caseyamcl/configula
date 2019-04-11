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

namespace Configula\Loader;

use Configula\ConfigValues;
use Configula\Exception\ConfigLoaderException;

/**
 * Class PhpFileLoader
 *
 * @package Configula\Loader
 */
class PhpFileLoader implements FileLoaderInterface
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

        if (is_readable($this->filePath)) {
            ob_start();

            /** @noinspection PhpIncludeInspection */
            include $this->filePath;

            ob_end_clean();
        }

        if (isset($config) && is_array($config)) {
            return new ConfigValues($config);
        } elseif (trim(file_get_contents($this->filePath)) === "") {
            return new ConfigValues([]);
        } else {
            throw new ConfigLoaderException("Missing or invalid \$config array in file: " . $this->filePath);
        }
    }
}
