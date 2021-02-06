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

namespace Configula\Loader;

/**
 * Class IniFileLoaderTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class IniFileLoaderTest extends AbstractFileLoaderTest
{
    /**
     * Get extension without the dot (.)
     *
     * @return string
     */
    protected function getExt(): string
    {
        return 'ini';
    }

    public function testMultiSectionFile()
    {
        $loader = new IniFileLoader($this->getTestFilePath('sections'), true);
        $values = $loader->load();

        $this->assertTrue($values->has('no_section_value'));
        $this->assertTrue($values->has('another_no_section_value'));
        $this->assertTrue($values->has('section_a'));
        $this->assertTrue($values->has('section_a.section_a_item'));
        $this->assertTrue($values->has('section_b'));
        $this->assertTrue($values->has('section_b.section_b_item'));
    }

    /**
     * @param  string $filename
     * @return FileLoaderInterface
     */
    protected function getObject(string $filename): FileLoaderInterface
    {
        return new IniFileLoader($filename);
    }
}
