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

namespace Configula;

use Configula\Exception\ConfigLogicException;
use Configula\Exception\ConfigValueNotFoundException;
use Configula\Util\ArrayUtils;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Class ConfigValuesTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class ConfigValuesTest extends TestCase
{
    /**
     * @return array
     */
    public static function getTestValues(): array
    {
        return [
            'a'       => 'Apple',
            'b.value' => 'Banana',
            'c'        => ['wumps' => 234.45, 'lumps' => 0],
            'd'        => null,
            'e'        => false
        ];
    }

    public static function existingDataProvider(): array
    {
        return [
            'basic value'            => ['a',       'Apple' ],
            'value with dot in name' => ['b.value', 'Banana'],
            'value in sub-array'     => ['c.wumps', 234.45  ],
            'existing NULL value'    => ['d',       null    ],
            'existing FALSE value'   => ['e',       false   ]
        ];
    }

    /**
     * @return array
     */
    public static function nonExistentDataProvider(): array
    {
        return [
            'top-level item does not exist'                     => ['g'],
            'top-level item exists, but does not contain value' => ['c.lamps'],
            'entire path does not exist'                        => ['d.right.on']
        ];
    }

    // --------------------------------------------------------------

    public function testInstantiateSucceeds(): void
    {
        $config = new ConfigValues(static::getTestValues());
        $this->assertInstanceOf(ConfigValues::class, $config);
    }

    #[DataProvider('existingDataProvider')]
    public function testFindReturnsValueIfExists(string $path, mixed $expected): void
    {
        $config = new ConfigValues(static::getTestValues());
        $this->assertSame($expected, $config->find($path));
    }

    #[DataProvider('nonExistentDataProvider')]
    public function testFindReturnsNullIfValueNotExists(string $path): void
    {
        $config = new ConfigValues(static::getTestValues());
        $this->assertNull($config->find($path));
    }

    #[DataProvider('existingDataProvider')]
    public function testHasReturnsTrueForExistentValues(string $path)
    {
        $config = new ConfigValues(static::getTestValues());
        $this->assertTrue($config->has($path));
    }

    #[DataProvider('nonExistentDataProvider')]
    public function testHasReturnsFalseForNonExistentValues(string $path): void
    {
        $config = new ConfigValues(static::getTestValues());
        $this->assertFalse($config->has($path));
    }

    #[DataProvider('existingDataProvider')]
    #[DataProvider('nonExistentDataProvider')]
    public function testHasValueReturnsExpectedResult(string $path): void
    {
        $config = new ConfigValues(static::getTestValues());
        $expected = $config->has($path) && (! in_array($config->get($path), [null, '', []], true));
        $this->assertSame($expected, $config->hasValue($path));
    }

    public function testArrayAccessForArrayWhenValueExists(): void
    {
        $config = new ConfigValues(static::getTestValues());
        $this->assertSame('Apple', $config['a']);
        $this->assertSame('Banana', $config['b.value']);
        $this->assertSame(234.45, $config['c.wumps']);
    }

    #[DataProvider('nonExistentDataProvider')]
    public function testArrayAccessThrowsExceptionWhenValueNotExists(string $nonExistentPath): void
    {
        $this->expectException(ConfigValueNotFoundException::class);
        $config = new ConfigValues(static::getTestValues());
        $config->get($nonExistentPath);
    }

    public function testArrayAccessReturnsExpectedValueForIsset(): void
    {
        $config = new ConfigValues(static::getTestValues());
        $this->assertTrue(isset($config['a']));
        $this->assertTrue(isset($config['c.wumps']));
        $this->assertFalse(isset($config['baz']));
    }

    public function testArrayAccessThrowsExceptionForArrayAccessWrite(): void
    {
        $this->expectException(ConfigLogicException::class);
        $config = new ConfigValues(static::getTestValues());
        $config['item'] = 'item';
    }

    public function testArrayAccessThrowsExceptionForArrayAccessUnset(): void
    {
        $this->expectException(ConfigLogicException::class);
        $config = new ConfigValues(static::getTestValues());
        unset($config['a']);
    }

    #[DataProvider('existingDataProvider')]
    public function testMagicGetMethodReturnsExpectedValueWhenExists(string $path, $expected): void
    {
        $config = new ConfigValues(static::getTestValues());
        $this->assertSame($expected, $config->$path);
    }

    #[DataProvider('nonExistentDataProvider')]
    public function testMagicGetMethodThrowsExceptionWhenValueNotExists(string $path): void
    {
        $this->expectException(ConfigValueNotFoundException::class);
        $config = new ConfigValues(static::getTestValues());
        /** @phpstan-ignore-next-line Ignore, because we need to attempt to access a value in order to test the error */
        $config->$path;
    }

    #[DataProvider('existingDataProvider')]
    #[DataProvider('nonExistentDataProvider')]
    public function testMagicIssetMethodReturnsExpectedValue(string $path): void
    {
        $config = new ConfigValues(static::getTestValues());
        $this->assertSame($config->has($path), isset($config->$path));
    }

    #[DataProvider('existingDataProvider')]
    public function testMagicInvokeMethodBehavesSameAsGetWhenValueExists(string $path): void
    {
        $config = new ConfigValues(static::getTestValues());
        $this->assertSame($config($path), $config->get($path));
    }

    #[DataProvider('nonExistentDataProvider')]
    public function testMagicInvokeMethodThrowsExceptionWhenValueNotExists(string $path): void
    {
        $this->expectException(ConfigValueNotFoundException::class);
        $config = new ConfigValues(static::getTestValues());
        $config($path);
    }

    public function testGetArrayCopyReturnsExpectedArray(): void
    {
        $config = new ConfigValues(static::getTestValues());
        $this->assertSame(static::getTestValues(), $config->getArrayCopy());
    }

    public function testCountReturnsCountOfAllPaths(): void
    {
        $config = new ConfigValues(static::getTestValues());

        // The test values has a sub-array ('c') with two items, so it counts as two
        $this->assertSame(count(static::getTestValues()) + 1, $config->count());
    }

    public function testGetIteratorReturnsFlattenedTree()
    {
        $config = new ConfigValues(static::getTestValues());

        $this->assertEquals(
            iterator_to_array(ArrayUtils::flattenAndIterate(static::getTestValues())),
            iterator_to_array($config->getIterator())
        );
    }

    public function testFromConfigValues(): void
    {
        $config = new ConfigValues(static::getTestValues());
        $newConfig = ConfigValues::fromConfigValues($config);

        $this->assertEquals($config->getArrayCopy(), $newConfig->getArrayCopy());
    }


    public function testMerge(): void
    {
        $config = new ConfigValues(static::getTestValues());
        $mergedConfig = $config->merge(new ConfigValues(['a' => 'Avacado', 'baz' => 'buzz']));

        // Merged should have clobbered the value of 'a'
        $this->assertSame('Avacado', $mergedConfig->get('a'));
        // Merged should have added 'baz'
        $this->assertSame('buzz', $mergedConfig->get('baz'));

        // Original should have original value ofor 'a'
        $this->assertEquals('Apple', $config->get('a'));
        // Original should not contain added value
        $this->assertFalse($config->has('baz'));
    }

    public function testMergeValues(): void
    {
        $config = new ConfigValues(static::getTestValues());
        $mergedConfig = $config->mergeValues(['a' => 'Avacado', 'baz' => 'buzz']);

        // Merged should have clobbered the value of 'a'
        $this->assertSame('Avacado', $mergedConfig->get('a'));
        // Merged should have added 'baz'
        $this->assertSame('buzz', $mergedConfig->get('baz'));

        // Original should have original value ofor 'a'
        $this->assertEquals('Apple', $config->get('a'));
        // Original should not contain added value
        $this->assertFalse($config->has('baz'));
    }
}
