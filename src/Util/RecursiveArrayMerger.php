<?php

namespace Configula\Util;

/**
 * Configula Utilities Class
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class RecursiveArrayMerger
{
    /**
     * Merge configuration arrays
     *
     * What I would wish that array_merge_recursive actually does...
     * From: http://www.php.net/manual/en/function.array-merge-recursive.php#102379
     *
     * @param  array $arr1 Array #2
     * @param  array $arr2 Array #1
     * @return array
     */
    public static function merge($arr1, $arr2)
    {
        foreach ($arr2 as $key => $value) {
            if (array_key_exists($key, $arr1) && is_array($value)) {
                $arr1[$key] = static::merge($arr1[$key], $arr2[$key]);
            } else {
                $arr1[$key] = $value;
            }
        }

        return $arr1;
    }
}
