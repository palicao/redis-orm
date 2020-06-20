<?php

namespace Tystr\RedisOrm\Criteria;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
abstract class Restriction implements RestrictionInterface
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param string     $key
     * @param mixed $value
     */
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @param RestrictionInterface $restriction
     *
     * @return bool
     */
    public function equals(RestrictionInterface $restriction): bool
    {
        return get_class($restriction) === get_class($this) &&
            $restriction->getKey() === $this->getKey() &&
            $restriction->getValue() === $this->getValue();
    }
}
