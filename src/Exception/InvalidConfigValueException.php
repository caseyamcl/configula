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

namespace Configula\Exception;

use RuntimeException;

/**
 * Invalid Config Value Exception
 *
 * This is not used in the Configula source itself, but provided for implementing libraries
 * (see Configula documentation)
 */
class InvalidConfigValueException extends RuntimeException implements ConfigExceptionInterface
{
    // pass ...
}
