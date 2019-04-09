<?php

namespace Configula\Loader;

use Configula\Exception\ConfigLoaderException;
use PHPUnit\Framework\TestCase;

/**
 * Class FolderLoaderTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class FolderLoaderTest extends TestCase
{
    public function testFolderLoaderThrowsExceptionForNonexistentDirectory()
    {
        $this->expectException(ConfigLoaderException::class);
        $this->expectExceptionMessage('is it a directory');
        (new FolderLoader(__DIR__ . '/nonexistent'))->load();
    }

    public function testValuesLoadedInCorrectOrder()
    {
        $values = (new FolderLoader(__DIR__ . '/../fixtures/folder'))->load();
        $this->assertSame('a_from_config_ini', $values->get('a'));
        $this->assertSame('b-from-dist-json', $values->get('b'));
        $this->assertSame('c_from_config_local_yml', $values->get('c'));
    }
}
