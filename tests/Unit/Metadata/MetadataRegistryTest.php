<?php

namespace Tystr\RedisOrm\Tests\Unit\Metadata;

use PHPUnit_Framework_TestCase;
use Tystr\RedisOrm\Metadata\AnnotationMetadataLoader;
use Tystr\RedisOrm\Metadata\Metadata;
use Tystr\RedisOrm\Metadata\MetadataRegistry;
use Tystr\RedisOrm\Tests\Unit\Model\Person;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class MetadataRegistryTest extends PHPUnit_Framework_TestCase
{
    public function testGetMetadataForWith()
    {
        $loader = $this->getMockBuilder(AnnotationMetadataLoader::class)->disableOriginalConstructor()->getMock();
        $registry = new MetadataRegistry($loader);
        $class = Person::class;

        $expectedMetadata = new Metadata();

        $loader->expects($this->once())
            ->method('load')
            ->with($class)
            ->willReturn($expectedMetadata);

        $metadata = $registry->getMetadataFor($class);
        $this->assertSame($expectedMetadata, $metadata);

        // The following call should not trigger a call to LoaderInterface::load()
        $metadata  = $registry->getMetadataFor($class);
        $this->assertSame($expectedMetadata, $metadata);
    }
}