<?php

namespace Tystr\RedisOrm\Criteria;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
interface RestrictionInterface
{
    /**
     * @return string
     */
    public function getKey(): string;

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param mixed $value
     */
    public function setValue($value): void;

    /**
     * @param RestrictionInterface $restriction
     * @return bool
     */
    public function equals(RestrictionInterface $restriction): bool;
}