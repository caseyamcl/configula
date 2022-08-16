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

namespace Configula\Util;

use Generator;

/**
 * Configula Utilities Class
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class ArrayUtils
{
    /**
     * Flatten and iterate
     *
     * @param  array  $array
     * @param  string $delimiter
     * @param  string $basePath
     * @return Generator
     */
    public static function flattenAndIterate(array $array, string $delimiter = '.', string $basePath = ''): Generator
    {
        foreach ($array as $key => $value) {
            $fullKey = implode($delimiter, array_filter([$basePath, $key]));
            if (is_array($value)) {
                yield from static::flattenAndIterate($value, $delimiter, $fullKey);
            } else {
                yield $fullKey => $value;
            }
        }
    }

    /**
     * Merge configuration arrays
     *
     * What I would wish that array_merge_recursive actually does.
     *
     * This is a cascading merge, with individual values being overwritten.
     * From: http://www.php.net/manual/en/function.array-merge-recursive.php#102379
     *
     * @param  array $arr1 Array #1
     * @param  array $arr2 Array #2
     * @return array
     */
    public static function merge(array $arr1, array $arr2): array
    {
        foreach ($arr2 as $key => $value) {
            if (array_key_exists($key, $arr1) && is_array($value) && is_array($arr1[$key])) {
                $arr1[$key] = static::merge($arr1[$key], $arr2[$key]);
            } else {
                $arr1[$key] = $value;
            }
        }

        return $arr1;
    }
}
