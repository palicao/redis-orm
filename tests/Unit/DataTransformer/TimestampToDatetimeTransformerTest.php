<?php

namespace Tystr\RedisOrm\Tests\Unit\DataTransformer;

use DateTime;
use PHPUnit_Framework_TestCase;
use Tystr\RedisOrm\DataTransformer\TimestampToDatetimeTransformer;
use Tystr\RedisOrm\Exception\InvalidArgumentException;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class TimestampToDatetimeTransformerTest extends PHPUnit_Framework_TestCase
{
    public function testTransform()
    {
        $transformer = new TimestampToDatetimeTransformer();
        assertEquals(new DateTime('2014-01-01'), $transformer->transform(1388534400));
    }

    public function testReverseTransform()
    {
        $transformer = new TimestampToDatetimeTransformer();
        assertEquals(1388534400, $transformer->reverseTransform(new DateTime('2014-01-01')));
    }

    public function testReverseTransformRequiresDateTimeObject()
    {
        $transformer = new TimestampToDatetimeTransformer();
        $this->setExpectedException(InvalidArgumentException::class);
        $transformer->reverseTransform(123);
    }
}