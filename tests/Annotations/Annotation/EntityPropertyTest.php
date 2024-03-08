<?php

namespace Annotations\Annotation;

use SQLI\EzToolboxBundle\Annotations\Annotation\EntityProperty;
use PHPUnit\Framework\TestCase;

class EntityPropertyTest extends TestCase
{
    public function testDefaultValues(): void
    {

        $entity = new EntityProperty();
        $this->assertFalse($entity->isReadonly());
        $this->assertTrue($entity->isVisible());
        $this->assertNull($entity->getChoices());
        $this->assertEquals('', $entity->getDescription());
        $this->assertEquals('', $entity->getExtraLink());
    }

    public function testSetVisible(): void
    {

        $entityAttribute = new EntityProperty();
        $entityAttribute->visible = true;
        $this->assertTrue($entityAttribute->isVisible());
        $entityAttribute->visible = false;
        $this->assertFalse($entityAttribute->isVisible());
    }
    public function testSetReadonly(): void
    {

        $entity = new EntityProperty();

        // Set a new value for readonly
        $entity->readonly = true;
        // Assert that the value of readonly has been updated
        $this->assertTrue($entity->isReadonly());

        // Set a new value for readonly
        $entity->readonly = false;
        // Assert that the value of readonly has been updated
        $this->assertFalse($entity->isReadonly());
    }
    public function testSetChoices(): void
    {

        $entityAttribute = new EntityProperty();
        $entityAttribute->choices = ['test'];
        $this->assertIsArray($entityAttribute->getChoices());
        $this->assertEquals(1, count($entityAttribute->getChoices()));
    }

    public function testSetDescription(): void
    {
        $entity = new EntityProperty();
        $description = "This is a new description";
        $entity->description = $description;
        $this->assertEquals($description, $entity->getDescription());
    }

    public function testSetExtraLink(): void
    {

        $entity = new EntityProperty();

        $link = "http://example.com";
        $entity->extra_link = $link;
        $this->assertEquals($link, $entity->getExtraLink());
        $link = '';
        $entity->extra_link = $link;
        $this->assertEmpty($link, $entity->getDescription());
    }
    public function testIsVisible(): void
    {
        $entity = new EntityProperty();
        $this->assertTrue($entity->isVisible());
    }

    public function testIsReadonly(): void
    {
        $entity = new EntityProperty();
        $this->assertIsBool($entity->isReadonly());
    }

    public function testIsDescritpion(): void
    {
        $entity = new EntityProperty();
        $this->assertIsString($entity->getDescription());
    }
}
