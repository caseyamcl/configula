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

namespace Configula\Loader;

use Configula\Exception\ConfigLoaderException;

/**
 * Class PhpFileLoaderTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class PhpFileLoaderTest extends AbstractFileLoaderTester
{
    public function testUnreadableFileInPhpFileLoaderThrowsException(): void
    {
        $this->expectException(ConfigLoaderException::class);
        $obj = $this->getObject('/non/existent/filepath.txt');
        $obj->load();
    }

    /**
     * Get extension without the dot (.)
     *
     * @return string
     */
    protected function getExt(): string
    {
        return 'php';
    }

    /**
     * @param  string $filename
     * @return FileLoaderInterface
     */
    protected function getObject(string $filename): FileLoaderInterface
    {
        return new PhpFileLoader($filename);
    }
}
