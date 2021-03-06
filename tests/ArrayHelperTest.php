<?php
/**
 * This file is part of Berlioz framework.
 *
 * @license   https://opensource.org/licenses/MIT MIT License
 * @copyright 2019 Ronan GIRON
 * @author    Ronan GIRON <https://github.com/ElGigi>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code, to the root.
 */

namespace Berlioz\Helpers\Tests;

use Berlioz\Helpers\ArrayHelper;
use PHPUnit\Framework\TestCase;

class ArrayHelperTest extends TestCase
{
    public function testIsSequential()
    {
        $this->assertTrue(ArrayHelper::isSequential(['foo', 'bar', 'hello', 'world']));
        $this->assertTrue(ArrayHelper::isSequential([0 => 'foo', 1 => 'bar', 2 => 'hello', 3 => 'world']));
        $this->assertTrue(ArrayHelper::isSequential([0 => 'foo', 2 => 'bar', 1 => 'hello', 3 => 'world']));
        $this->assertTrue(ArrayHelper::isSequential(['0' => 'foo', '2' => 'bar', '1' => 'hello', '3' => 'world']));

        $this->assertFalse(ArrayHelper::isSequential(['bar' => 'foo', 'foo' => 'bar', '1' => 'hello', '3' => 'world']));
        $this->assertFalse(ArrayHelper::isSequential(['bar' => 'foo', 'foo' => 'bar', 1 => 'hello', 3 => 'world']));
        $this->assertFalse(ArrayHelper::isSequential(['00' => 'foo', '01' => 'bar', '02' => 'hello', '03' => 'world']));
    }

    public function testMergeRecursive()
    {
        $arr1 = ['foo'  => 'hello',
                 'bar'  => 'world',
                 'test' => ['foo', 'bar', 'hello' => 'world']];
        $arr2 = ['test' => ['hello', 'foo']];
        $arr3 = ['foo' => 'world'];
        $arr4 = ['foo'  => 'world',
                 'test' => ['hello' => 'world2']];
        $arr5 = ['foo'  => 'world',
                 'test' => ['hello' => ['world2', 'world3']]];

        $this->assertEquals(['foo'  => 'hello',
                             'bar'  => 'world',
                             'test' => ['foo', 'bar', 'hello', 'foo', 'hello' => 'world']],
                            ArrayHelper::mergeRecursive($arr1, $arr2));
        $this->assertEquals(['foo'  => 'world',
                             'bar'  => 'world',
                             'test' => ['foo', 'bar', 'hello' => 'world']],
                            ArrayHelper::mergeRecursive($arr1, $arr3));
        $this->assertEquals(['foo'  => 'world',
                             'bar'  => 'world',
                             'test' => ['foo', 'bar', 'hello' => 'world2']],
                            ArrayHelper::mergeRecursive($arr1, $arr4));
        $this->assertEquals(['foo'  => 'world',
                             'bar'  => 'world',
                             'test' => ['foo', 'bar', 'hello' => ['world2', 'world3']]],
                            ArrayHelper::mergeRecursive($arr1, $arr5));
    }

    public function testTraverseGet()
    {
        $tArray = [
            'foo'  => 'bar',
            'foo2' => [
                'foo3' => ['foo4' => 'bar4'],
                'foo5' => 'bar5',
                'foo6' => ['foo7' => 'bar7',
                           'foo8' => 'bar8'],
            ],
        ];

        $this->assertEquals('bar', ArrayHelper::traverseGet($tArray, 'foo'));
        $this->assertEquals('bar8', ArrayHelper::traverseGet($tArray, 'foo2.foo6.foo8'));
        $this->assertEquals(null, ArrayHelper::traverseGet($tArray, 'foo2.foo999.foo8'));
        $this->assertEquals(null, ArrayHelper::traverseGet($tArray, 'foo3.foo4'));
        $this->assertEquals(null, ArrayHelper::traverseGet($tArray, 'foo.bar.foo'));
    }

    public function testTraverseSet()
    {
        $tArray = [
            'foo'  => 'bar',
            'foo2' => [
                'foo3' => ['foo4' => 'bar4'],
                'foo5' => 'bar5',
                'foo6' => ['foo7' => 'bar7',
                           'foo8' => 'bar8'],
            ],
        ];

        $this->assertTrue(ArrayHelper::traverseSet($tArray, 'foo', 'bob'));
        $this->assertEquals('bob', ArrayHelper::traverseGet($tArray, 'foo'));
        $this->assertTrue(ArrayHelper::traverseSet($tArray, 'foo2.foo6.foo8', 'bob8'));
        $this->assertEquals('bob8', ArrayHelper::traverseGet($tArray, 'foo2.foo6.foo8'));
        $this->assertTrue(ArrayHelper::traverseSet($tArray, 'foo2.foo999.foo8', 'bob999'));
        $this->assertEquals('bob999', ArrayHelper::traverseGet($tArray, 'foo2.foo999.foo8'));
        $this->assertFalse(ArrayHelper::traverseSet($tArray, 'foo.bar.foo', 'bar'));
    }
}
