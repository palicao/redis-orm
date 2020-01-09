<?php

namespace Tystr\RedisOrm\Hydrator;

use ReflectionClass;
use ReflectionException;
use Tystr\RedisOrm\DataTransformer\DataTypes;
use Tystr\RedisOrm\DataTransformer\TimestampToDatetimeTransformer;
use Tystr\RedisOrm\Metadata\Metadata;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class ObjectHydrator implements ObjectHydratorInterface
{
    /**
     * @param object $object
     * @param array $data
     * @param Metadata $metadata
     * @return object
     * @throws ReflectionException
     */
    public function hydrate($object, array $data, Metadata $metadata)
    {
        $reflClass = new ReflectionClass(get_class($object));
        foreach ($reflClass->getProperties() as $property) {
            $mapping = $metadata->getPropertyMapping($property->getName());
            if (null == $mapping) {
                continue;
            }
            $property->setAccessible(true);
            if (DataTypes::COLLECTION == $mapping['type']) {
                $value = [];
                foreach (array_keys($data) as $key) {
                    if (0 === stripos($key, $mapping['name'].':')) {
                        $value[] = $data[$key];
                    }
                }
                $property->setValue($object, $value);

                continue;
            } elseif (DataTypes::HASH == $mapping['type']) {
                $value = [];
                foreach (array_keys($data) as $key) {
                    if (0 === stripos($key, $mapping['name'].':')) {
                        $newKey = substr($key, strrpos($key, ':')+1);
                        $value[$newKey] = $data[$key];
                    }
                }
                $property->setValue($object, $value);

                continue;
            }

            if (!isset($data[$mapping['name']])) {
                $data[$mapping['name']] = null;
            }
            $property->setValue($object, $this->transformValue($mapping['type'], $data[$mapping['name']]));
        }

        return $object;
    }

    /**
     * @param object $object
     * @param Metadata $metadata
     * @return array
     * @throws ReflectionException
     */
    public function toArray($object, Metadata $metadata)
    {
        $reflClass = new ReflectionClass(get_class($object));
        $data = [];
        foreach ($reflClass->getProperties() as $property) {
            $mapping = $metadata->getPropertyMapping($property->getName());
            if (null === $mapping) {
                continue;
            }
            $property->setAccessible(true);
            if ($mapping['type'] === DataTypes::COLLECTION || DataTypes::HASH == $mapping['type']) {
                foreach ((array)$property->getValue($object) as $key => $value) {
                    $data[$mapping['name'].':'.$key] = $value;
                }
            } else {
                $data[$mapping['name']] = $this->reverseTransformValue($mapping['type'], $property->getValue($object));
            }
        }

        return $data;
    }

    /**
     * @param string $type
     * @param mixed $value
     * @return mixed
     */
    protected function transformValue($type, $value)
    {
        switch ($type) {
            case DataTypes::STRING:
                return (string)$value;
            case DataTYpes::INTEGER:
                return (int)$value;
            case DataTypes::DOUBLE:
                return (float)$value;
            case DataTYpes::BOOLEAN:
                return (bool) $value;
            case DataTypes::COLLECTION:
                return (array) $value;
            case DataTypes::DATE:
                if (null === $value || '' === $value) {
                    return null;
                }
                $transformer = new TimestampToDatetimeTransformer();

                return $transformer->transform($value);
            default:
                // @todo Lookup custom data transformer for custom configured types?
                return null;
        }
    }

    /**
     * @param string $type
     * @param mixed $value
     * @return mixed|string
     */
    protected function reverseTransformValue($type, $value)
    {
        if ($type === DataTypes::DATE && $value instanceof \DateTime) {
            $transformer = new TimestampToDatetimeTransformer();

            return $transformer->reverseTransform($value);
        }

        if ($type === DataTypes::BOOLEAN) {
            return (int)$value;
        }

        return $value;
    }
}
