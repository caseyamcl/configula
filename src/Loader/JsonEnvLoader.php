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
use Configula\Exception\ConfigLoaderException;

/**
 * Json Env Loader
 *
 * Loads JSON tree from single environment variable
 *
 * @package Configula\Loader
 */
final class JsonEnvLoader implements ConfigLoaderInterface
{
    /**
     * @var string
     */
    private $envValueName;

    /**
     * @var bool
     */
    private $asAssoc;

    /**
     * JsonEnvLoader constructor.
     *
     * @param string $envValueName
     * @param bool   $asAssoc
     */
    public function __construct(string $envValueName, bool $asAssoc = false)
    {
        $this->envValueName = $envValueName;
        $this->asAssoc = $asAssoc;
    }

    /**
     * Load config
     *
     * @return ConfigValues
     */
    public function load(): ConfigValues
    {
        $rawContent = getenv($this->envValueName);

        if (! $decoded = @json_decode($rawContent, $this->asAssoc)) {
            throw new ConfigLoaderException("Could not parse JSON from environment variable: " . $this->envValueName);
        }

        return new ConfigValues((array) $decoded);
    }
}
