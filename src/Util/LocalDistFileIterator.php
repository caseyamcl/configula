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
use IteratorAggregate;
use ReturnTypeWillChange;
use SplFileInfo;

/**
 * Local/Dist file iterator
 *
 * Iterates over files in the following order:
 *
 * *.dist.EXT  ('.dist' is configurable)
 * *.EXT
 * *.local.EXT ('.local' is configurable)
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class LocalDistFileIterator implements IteratorAggregate
{
    /**
     * @var iterable
     */
    private $iterator;

    /**
     * @var string
     */
    private $localSuffix;

    /**
     * @var string
     */
    private $distSuffix;

    /**
     * LocalDistFileIterator constructor.
     * @param iterable|SplFileInfo[]|string[] $fileIterator Iterate either file paths or SplFileInfo instances
     * @param string $localSuffix  File suffix denoting 'local' (high priority) files (always comes before extension)
     * @param string $distSuffix  File suffix denoting 'dist' (low priority) files (always comes before extension)
     */
    public function __construct(iterable $fileIterator, string $localSuffix = '.local', string $distSuffix = '.dist')
    {
        $this->iterator = $fileIterator;
        $this->localSuffix = $localSuffix;
        $this->distSuffix = $distSuffix;
    }

    /**
     * @return Generator<int,SplFileInfo>
     */
    #[ReturnTypeWillChange]
    public function getIterator()
    {
        $localFiles = [];
        $normalFiles = [];

        foreach ($this->iterator as $file) {
            $basename = rtrim($file->getBasename(strtolower($file->getExtension())), '.');

            if (strcasecmp(substr($basename, 0 - strlen($this->localSuffix)), $this->localSuffix) === 0) {
                $localFiles[] = $file;
            } elseif (strcasecmp(substr($basename, 0 - strlen($this->distSuffix)), $this->distSuffix) === 0) {
                yield $file;
            } else {
                $normalFiles[] = $file;
            }
        }

        foreach (array_merge($normalFiles, $localFiles) as $item) {
            yield $item;
        }
    }
}
