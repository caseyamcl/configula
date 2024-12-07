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

use Configula\ConfigValues;
use Configula\Exception\ConfigLoaderException;

final readonly class IniFileLoader implements FileLoaderInterface
{
    public function __construct(
        private string $filePath,
        private bool $processSections = true
    ) {
    }

    public function load(): ConfigValues
    {
        $values = @parse_ini_file($this->filePath, $this->processSections, INI_SCANNER_TYPED);

        if ($values === false) {
            throw new ConfigLoaderException("Error parsing INI file: " . $this->filePath);
        }

        return new ConfigValues($values);
    }
}
