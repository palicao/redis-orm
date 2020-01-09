<?php

namespace Tystr\RedisOrm\Tests\Unit\KeyNamingStrategy;

use PHPUnit\Framework\TestCase;
use Tystr\RedisOrm\KeyNamingStrategy\ColonDelimitedKeyNamingStrategy;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class ColonDelimitedKeyNamingStrategyTest extends TestCase
{
    public function testGetKeyName()
    {
        $parts = array('prefix', 'user', 123456);
        $strategy = new ColonDelimitedKeyNamingStrategy();

        assertEquals('prefix:user:123456', $strategy->getKeyName($parts));
    }
}