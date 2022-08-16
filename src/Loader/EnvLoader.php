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

use Configula\ConfigLoaderInterface;
use Configula\ConfigValues;
use Configula\Filter\ExtractTopLevelItemsFilter;
use Configula\Filter\RemovePrefixFilter;
use Dflydev\DotAccessData\Data;

/**
 * Env Loader
 *
 * Loads configuration from each environment variable
 *
 * @package Configula\Loader
 */
final class EnvLoader implements ConfigLoaderInterface
{
    /**
     * @var string
     */
    private $regex;

    /**
     * @var null|string
     */
    private $delimiter;

    /**
     * @var bool
     */
    private $toLower;

    /**
     * Load from environment looking only for those values with a specified prefix (and remove prefix)
     *
     * @param  string      $prefix    Specify a prefix, and only environment variables with this prefix will be read
     *                                (e.g. "MYAPP_" means that this will only read env vars starting with
     *                                "MYAPP_")' Values will be
     * @param  string      $delimiter Split variable names on this string into a nested array.  (e.g. "MYSQL_HOST"
     *                                would become the key, "MYSQL.HOST" (empty string to not delimit)
     * @param  bool        $toLower   Convert all keys to lower-case
     * @return ConfigValues
     */
    public static function loadUsingPrefix(string $prefix, string $delimiter = '', bool $toLower = false): ConfigValues
    {
        $prefix = preg_quote($prefix);
        $values = (new EnvLoader("/^{$prefix}/", $delimiter, $toLower))->load();

        return $delimiter
            ? (new ExtractTopLevelItemsFilter($toLower ? strtolower($prefix) : $prefix, $delimiter))->__invoke($values)
            : (new RemovePrefixFilter($toLower ? strtolower($prefix) : $prefix))->__invoke($values);
    }

    /**
     * Environment Loader Constructor
     *
     * @param string      $regex     Optionally filter values based on some regex pattern
     * @param string      $delimiter Split variable names on this string into a nested array.  (e.g. "MYSQL_HOST"
     *                               would become the key, "MYSQL.HOST" (empty string to not delimit)
     * @param bool        $toLower   Convert all keys to lower-case
     */
    public function __construct(string $regex = '', string $delimiter = '', bool $toLower = false)
    {
        $this->regex     = $regex;
        $this->delimiter = $delimiter;
        $this->toLower   = $toLower;
    }

    /**
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        $configValues = new Data();

        // Make sure we capture *ALL* environment values
        $envValues = array_merge(getenv(), $_ENV);

        foreach ($envValues as $valName => $valVal) {
            if ($this->regex && ! preg_match($this->regex, $valName)) {
                continue;
            }

            $valName = ($this->delimiter) ? str_replace($this->delimiter, '.', $valName) : $valName;
            $valName = ($this->toLower) ? strtolower($valName) : $valName;
            $configValues->set($valName, $this->prepareVal((string) $valVal));
        }

        return new ConfigValues($configValues->export());
    }

    /**
     * Prepare string value
     *
     * @param  string $value
     * @return bool|float|int|string|null
     */
    private function prepareVal(string $value)
    {
        if (is_numeric($value)) {
            return filter_var($value, FILTER_VALIDATE_INT) !== false ? (int) $value : (float) $value;
        }

        switch (strtolower($value)) {
            case 'null':
                return null;
            case 'false':
                return false;
            case 'true':
                return true;
            default:
                return $value;
        }
    }
}
