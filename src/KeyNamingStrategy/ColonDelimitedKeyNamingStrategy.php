<?php

namespace Tystr\RedisOrm\KeyNamingStrategy;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class ColonDelimitedKeyNamingStrategy implements KeyNamingStrategyInterface
{
    /**
     * @param array $parts
     * @return string
     */
    public function getKeyName(array $parts): string
    {
        return rtrim(implode(':', $parts), ':');
    }
}
