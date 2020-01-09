<?php

namespace Tystr\RedisOrm\Tests\Unit\KeyNamingStrategy;

use PHPUnit_Framework_TestCase;
use Tystr\RedisOrm\KeyNamingStrategy\ColonDelimitedKeyNamingStrategy;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class ColonDelimitedKeyNamingStrategyTest extends PHPUnit_Framework_TestCase
{
    public function testGetKeyName()
    {
        $parts = array('prefix', 'user', 123456);
        $strategy = new ColonDelimitedKeyNamingStrategy();

        assertEquals('prefix:user:123456', $strategy->getKeyName($parts));
    }
}