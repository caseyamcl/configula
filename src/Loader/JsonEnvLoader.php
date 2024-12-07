<?php

/**
 * Configula Library
 *
 * @license http://opensource.org/licenses/MIT
 * @link https://github.com/caseyamcl/configula
 * @version 5
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

final readonly class JsonEnvLoader implements ConfigLoaderInterface
{
    public function __construct(
        private string $envValueName,
        private bool $asAssoc = false
    ) {
    }

    public function load(): ConfigValues
    {
        $rawContent = getenv($this->envValueName);

        if (! $decoded = @json_decode($rawContent, $this->asAssoc)) {
            throw new ConfigLoaderException("Could not parse JSON from environment variable: " . $this->envValueName);
        }

        return new ConfigValues((array) $decoded);
    }
}
