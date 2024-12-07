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

/**
 * Class JsonFileLoaderTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class JsonFileLoaderTest extends AbstractFileLoaderTester
{
    public function testExpectedObjectIsObject(): void
    {
        $config = $this->getObject($this->getTestFilePath('config'))->load();
        $this->assertIsObject($config->get('c'));
    }

    /**
     * Get extension without the dot (.)
     *
     * @return string
     */
    protected function getExt(): string
    {
        return 'json';
    }

    /**
     * @param  string $filename
     * @return FileLoaderInterface
     */
    protected function getObject(string $filename): FileLoaderInterface
    {
        return new JsonFileLoader($filename);
    }
}
