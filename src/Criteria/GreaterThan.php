<?php

namespace Tystr\RedisOrm\Criteria;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class GreaterThan extends Restriction implements GreaterThanInterface
{
    /**
     * @return int
     */
    public function getValue()
    {
        return (int) $this->value;
    }
}
