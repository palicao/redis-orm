<?php

namespace Tystr\RedisOrm\Metadata;

use ReflectionException;
use Tystr\RedisOrm\DataTransformer\DataTypes;
use Tystr\RedisOrm\Exception\InvalidArgumentException;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class Metadata
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var array
     */
    protected $indexes = [];

    /**
     * @var array
     */
    protected $sortedIndexes = [];

    /**
     * keys: name
     * @var array
     */
    protected $propertyMappings = [];

    /**
     * @return array
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }

    /**
     * @param array $indexes
     */
    public function setIndexes(array $indexes): void
    {
        $this->indexes = $indexes;
    }

    /**
     * @param string $property
     * @param string $index
     */
    public function addIndex(string $property, string $index): void
    {
        $this->indexes[$property] = $index;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasIndex($name): bool
    {
        return isset($this->indexes[$name]);
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * @return array
     */
    public function getSortedIndexes(): array
    {
        return $this->sortedIndexes;
    }

    /**
     * @param array $sortedIndexes
     */
    public function setSortedIndexes(array $sortedIndexes): void
    {
        $this->sortedIndexes = $sortedIndexes;
    }

    /**
     * @param string $propertyName
     * @param string $sortedIndex
     */
    public function addSortedIndex(string $propertyName, string $sortedIndex)
    {
        $this->sortedIndexes[$propertyName] = $sortedIndex;
    }

    /**
     * @param string $propertyName
     * @return string|null
     */
    public function getSortedIndex($propertyName): ?string
    {
        if (isset($this->sortedIndexes[$propertyName])) {
            return $this->sortedIndexes[$propertyName];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return array
     */
    public function getPropertyMappings(): array
    {
        return $this->propertyMappings;
    }

    /**
     * @param array $propertyMappings
     */
    public function setPropertyMappings(array $propertyMappings): void
    {
        $this->propertyMappings = $propertyMappings;
    }

    /**
     * @param string $propertyName
     * @param array $mapping
     * @throws ReflectionException
     */
    public function addPropertyMapping(string $propertyName, array $mapping): void
    {
        if (!isset($mapping['type'])) {
            throw new InvalidArgumentException(sprintf('Invalid @Field mapping for property "%s".', $propertyName));
        }
        if (!DataTypes::isValidDataType($mapping['type'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid @Field mapping for property "%s": the specified type "%s" is invalid.',
                    $propertyName,
                    $mapping['type']
                )
            );
        }
        $this->propertyMappings[$propertyName]['type'] = $mapping['type'];
        $this->propertyMappings[$propertyName]['name'] = isset($mapping['name']) && null !== $mapping['name'] ?
            $mapping['name'] : $propertyName;
    }

    /**
     * @param string $propertyName
     * @return null
     */
    public function getPropertyMapping($propertyName)
    {
        if (isset($this->propertyMappings[$propertyName])) {
            return $this->propertyMappings[$propertyName];
        }

        return null;
    }

    /**
     * @param string $mappedName
     * @return null|string
     */
    public function getMappingForMappedName(string $mappedName)
    {
        foreach ($this->propertyMappings as $propertyName => $mapping) {
            if ($mappedName === $mapping['name']) {
                $mapping['propertyName'] = $propertyName;

                return $mapping;
            }
        }
        return null;
    }

    /**
     * @param array $array
     * @return Metadata
     */
    public static function __set_state(array $array)
    {
        $metadata = new static();
        $metadata->setId($array['id']);
        $metadata->setPrefix($array['prefix']);
        $metadata->setIndexes($array['indexes']);
        $metadata->setSortedIndexes($array['sortedIndexes']);
        $metadata->setPropertyMappings($array['propertyMappings']);

        return $metadata;
    }
}