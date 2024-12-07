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

namespace Configula\fixtures;

use Configula\Loader\AbstractFileLoader;
use Error;

class ErrorTriggeringFileLoader extends AbstractFileLoader
{
    /**
     * Parse the contents
     *
     * @param  string $rawFileContents
     * @return array
     */
    protected function parse(string $rawFileContents): array
    {
        throw new Error('This is a test');
    }
}
