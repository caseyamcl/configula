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

namespace Configula\Exception;

/**
 * Config File not found
 *
 * This is thrown when an expected config file is missing
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class ConfigFileNotFoundException extends ConfigLoaderException
{
    // pass ...
}
