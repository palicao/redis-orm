<?php

namespace Tystr\RedisOrm\DataTransformer;

use ReflectionClass;
use ReflectionException;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
final class DataTypes
{
    public const DATE = 'date';
    public const STRING = 'string';
    public const INTEGER = 'integer';
    public const DOUBLE = 'double';
    public const BOOLEAN = 'boolean';

    /**
     * Denotes a numeric indexed array
     */
    public const COLLECTION = 'collection';

    /**
     * Denotes an associative array
     */
    public const HASH = 'hash';

    /**
     * @param string $dataType
     * @return bool
     * @throws ReflectionException
     */
    public static function isValidDataType($dataType)
    {
        $reflClass = new ReflectionClass(new static());
        $constants = $reflClass->getConstants();

        return in_array($dataType, $constants);
    }
}
