<?php

namespace Configula\Filter;

use Configula\ConfigValues;
use Configula\Exception\ConfigLoaderException;
use PHPUnit\Framework\TestCase;

/**
 * Class ExtractTopLevelItemFilterTest
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class ExtractTopLevelItemFilterTest extends TestCase
{
    public function testTopLevelExtractedWhenExists()
    {
        $test = [
            'pre' => [
                'a' => 'A',
                'b' => 'B',
                'c' => 'C'
            ],
            'foo' => 'bar'
        ];

        $values = (new ExtractTopLevelItemFilter('pre'))->__invoke(new ConfigValues($test));
        $this->assertEquals('A', $values->get('a'));
        $this->assertEquals('B', $values->get('b'));
    }

    public function testNoChangeWhenTopLevelNotExists()
    {
        $test = [
            'baz' => 'biz',
            'foo' => 'bar'
        ];

        $values = (new ExtractTopLevelItemFilter('pre'))->__invoke(new ConfigValues($test));
        $this->assertEquals($test, $values->getArrayCopy());
    }

    public function testExceptionThrownWhenCollisionDetected()
    {
        $this->expectException(ConfigLoaderException::class);
        $this->expectExceptionMessage('collision');

        $test = [
            'pre' => [
                'a' => 'A',  // conflicts with top-level item
                'b' => 'B',
                'c' => 'C'
            ],
            'a'  => 'Apple',
            'foo' => 'bar'
        ];

        (new ExtractTopLevelItemFilter('pre'))->__invoke(new ConfigValues($test));

    }
}
