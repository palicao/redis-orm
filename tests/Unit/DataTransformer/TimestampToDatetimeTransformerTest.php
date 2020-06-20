<?php

namespace Tystr\RedisOrm\Tests\Unit\DataTransformer;

use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Tystr\RedisOrm\DataTransformer\TimestampToDatetimeTransformer;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class TimestampToDatetimeTransformerTest extends TestCase
{
    public function testTransform()
    {
        $transformer = new TimestampToDatetimeTransformer();
        assertEquals(new DateTimeImmutable('2014-01-01'), $transformer->transform(1388534400));
    }

    public function testReverseTransform()
    {
        $transformer = new TimestampToDatetimeTransformer();
        assertEquals(1388534400, $transformer->reverseTransform(new DateTime('2014-01-01')));
    }
}
