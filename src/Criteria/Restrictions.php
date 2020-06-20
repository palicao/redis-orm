<?php

namespace Tystr\RedisOrm\Criteria;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class Restrictions
{
    /**
     * @param string $key
     * @param int    $value
     *
     * @return GreaterThan
     */
    public static function greaterThan(string $key, int $value): GreaterThanInterface
    {
        return new GreaterThan($key, $value);
    }

    /**
     * @param string $key
     * @param int    $value
     *
     * @return LessThan
     */
    public static function lessThan(string $key, int $value): LessThanInterface
    {
        return new LessThan($key, $value);
    }

    /**
     * @param string $key
     * @param int    $value
     *
     * @return EqualTo
     */
    public static function equalTo(string $key, int $value): EqualToInterface
    {
        return new EqualTo($key, $value);
    }

    /**
     * @param string        $key
     * @param Restriction[] $value
     *
     * @return AndGroup
     */
    public static function andGroup(string $key, array $value): AndGroupInterface
    {
        return new AndGroup($key, $value);
    }

    /**
     * @param string        $key
     * @param Restriction[] $value
     *
     * @return OrGroup
     */
    public static function orGroup(string $key, array $value): OrGroupInterface
    {
        return new OrGroup($key, $value);
    }
}
