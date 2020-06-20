<?php

namespace Tystr\RedisOrm\Query;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class ZRangeByScore
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var int|string
     */
    protected $min;

    /**
     * @var int|string
     */
    protected $max;

    /**
     * @param string $key
     * @param string $min
     * @param string $max
     */
    public function __construct(string $key, $min = '-inf', $max = '+inf')
    {
        $this->key = $key;
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return int|string
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param int|string $max
     */
    public function setMax($max): void
    {
        $this->max = $max;
    }

    /**
     * @return int|string
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param int|string $min
     */
    public function setMin($min): void
    {
        $this->min = $min;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [$this->key, $this->min, $this->max];
    }
}
