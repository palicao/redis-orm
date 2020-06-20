<?php

namespace Tystr\RedisOrm\DataTransformer;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class TimestampToDatetimeTransformer
{
    /**
     * @param mixed $value
     * @return DateTimeInterface
     */
    public function transform($value): DateTimeInterface
    {
        return DateTimeImmutable::createFromFormat('U', $value);
    }

    /**
     * @param DateTimeInterface $value
     * @return int
     */
    public function reverseTransform(DateTimeInterface $value): int
    {
        return (int) $value->format('U');
    }
}
