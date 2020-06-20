<?php

namespace Tystr\RedisOrm\Criteria;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class LessThan extends Restriction implements LessThanInterface
{
    /**
     * @return int
     */
    public function getValue(): int
    {
        return (int) $this->value;
    }
}
