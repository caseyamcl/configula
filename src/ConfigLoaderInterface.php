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

namespace Configula;

use Configula\Exception\ConfigLoaderException;

interface ConfigLoaderInterface
{
    /**
     * Load config
     * @throws ConfigLoaderException  If loading fails for whatever reason, throw this exception
     */
    public function load(): ConfigValues;
}
