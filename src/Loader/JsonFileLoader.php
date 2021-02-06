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

use Configula\Exception\ConfigLoaderException;

/**
 * Class JsonFileLoader
 *
 * @package Configula\Loader
 */
final class JsonFileLoader extends AbstractFileLoader
{
    /**
     * Parse the contents
     *
     * @param  string $rawFileContents
     * @return array
     */
    protected function parse(string $rawFileContents): array
    {
        if (trim($rawFileContents) === '') {
            return [];
        } elseif (! $decoded = @json_decode($rawFileContents)) {
            throw new ConfigLoaderException("Could not parse JSON file: " . $this->getFilePath());
        }

        return (array) $decoded;
    }
}
