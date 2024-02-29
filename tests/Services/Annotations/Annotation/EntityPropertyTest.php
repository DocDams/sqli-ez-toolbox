<?php

namespace Annotations\Annotation;

use SQLI\EzToolboxBundle\Annotations\Annotation\EntityProperty;
use PHPUnit\Framework\TestCase;


class EntityPropertyTest extends TestCase
{

    public function test__construct()
    {
        $entity = new EntityProperty();
        $this->assertInstanceOf(EntityProperty::CLASS, $entity);

    }
    public function testDefaultValues()
    {
        // Create a new instance of SQLIToolBoxEntity without passing any arguments
        $entity = new EntityProperty();

        // Assert that default values are correctly set
        $this->assertFalse($entity->isReadonly());
        $this->assertTrue($entity->isVisible());
        $this->assertNull($entity->getChoices());
        $this->assertEquals('', $entity->getDescription());
        $this->assertEquals('', $entity->getExtraLink());

    }

    public function testSetVisible() {
        // Create a new instance of SQLIToolBoxEntity
        $entityAttribute = new EntityProperty();
        // Set a new value for create
        $entityAttribute->visible = true;
        // Assert that the value of create has been updated
        $this->assertTrue($entityAttribute->isVisible());
        $entityAttribute->visible = false;
        // Assert that the value of create has been updated
        $this->assertFalse($entityAttribute->isVisible());

    }
    public function testSetReadonly()
    {
        // Create a new instance of YourClass
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
    public function testSetChoices() {
        // Create a new instance of SQLIToolBoxEntity
        $entityAttribute = new EntityProperty();
        // Set a new value for create
        $entityAttribute->choices = ['test'];
        // Assert that the value of create has been updated
        $this->assertIsArray($entityAttribute->getChoices());
        $this->assertEquals(1, count($entityAttribute->getChoices()));
    }

    public function testSetDescription()
    {
        // Create a new instance of YourClass
        $entity = new EntityProperty();

        // Set a new description
        $description = "This is a new description";
        $entity->description = $description;
        // Assert that the description has been updated
        $this->assertEquals($description, $entity->getDescription());

    }

    public function testSetExtraLink()
    {
        // Create a new instance of YourClass
        $entity = new EntityProperty();

        // Set a new extra link
        $link = "http://example.com";
        $entity->extra_link = $link;
        // Assert that the extra link has been updated
        $this->assertEquals($link, $entity->getExtraLink());
        $link = '';
        $entity->extra_link = $link;
        // Assert that the description has been updated
        $this->assertEmpty($link, $entity->getDescription());
    }
    public function testIsVisible()
    {
        $entity = new EntityProperty();
        $this->assertTrue($entity->isVisible());
    }

    public function testIsReadonly()
    {
        $entity = new EntityProperty();
        $this->assertIsBool($entity->isReadonly());
    }

    public function testIsDescritpion()
    {
        $entity = new EntityProperty();
        $this->assertIsString($entity->getDescription());
    }
}
