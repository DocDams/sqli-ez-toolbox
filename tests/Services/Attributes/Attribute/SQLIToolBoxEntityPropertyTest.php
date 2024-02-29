<?php

namespace SQLI\EzToolboxBundle\tests\Services\Attributes\Attribute;

use PHPUnit\Framework\TestCase;
use SQLI\EzToolboxBundle\Attributes\Attribute\SQLIToolBoxEntityProperty;


class SQLIToolBoxEntityPropertyTest extends TestCase
{

    public function test__construct()
    {
        $entity = new SQLIToolBoxEntityProperty(visible: true, readonly: true, description: "testDescription", choices: [], extra_link: "testExtra");
        $this->assertInstanceOf(SQLIToolBoxEntityProperty::CLASS,$entity);

    }
    public function testDefaultValues()
    {
        $entity = new SQLIToolBoxEntityProperty();
        $this->assertFalse($entity->isReadonly());
        $this->assertTrue($entity->isVisible());
        $this->assertNull($entity->getChoices());
        $this->assertEquals('', $entity->getDescription());
        $this->assertEquals('', $entity->getExtraLink());

    }

    public function testSetVisible() {
        $entityAttribute = new SQLIToolBoxEntityProperty();
        $entityAttribute->visible = true;
        $this->assertTrue($entityAttribute->isVisible());
        $entityAttribute->visible = false;
        $this->assertFalse($entityAttribute->isVisible());

    }
    public function testSetReadonly()
    {
        $entity = new SQLIToolBoxEntityProperty();
        $entity->readonly = true;
        $this->assertTrue($entity->isReadonly());
        $entity->readonly = false;
        $this->assertFalse($entity->isReadonly());
    }
    public function testSetChoices() {
        $entityAttribute = new SQLIToolBoxEntityProperty();
        $entityAttribute->choices = ['test'];
        $this->assertIsArray($entityAttribute->getChoices());
        $this->assertEquals(1, count($entityAttribute->getChoices()));
    }

    public function testSetDescription()
    {
        $entity = new SQLIToolBoxEntityProperty();
        $description = "This is a new description";
        $entity->description = $description;
        $this->assertEquals($description, $entity->getDescription());

    }

    public function testSetExtraLink()
    {
        $entity = new SQLIToolBoxEntityProperty();
        $link = "http://example.com";
        $entity->extra_link = $link;
        $this->assertEquals($link, $entity->getExtraLink());
        $link = '';
        $entity->extra_link = $link;
        $this->assertEmpty($link, $entity->getDescription());
    }
    public function testIsVisible()
    {
        $entity = new SQLIToolBoxEntityProperty();
        $this->assertTrue($entity->isVisible());
    }

    public function testIsReadonly()
    {
        $entity = new SQLIToolBoxEntityProperty();
        $this->assertIsBool($entity->isReadonly());
    }
    public function testIsChoices()
    {
        $entity = new SQLIToolBoxEntityProperty(choices: ['array']);
        $this->assertIsArray($entity->getChoices());
    }
    public function testIsDescritpion()
    {
        $entity = new SQLIToolBoxEntityProperty();
        $this->assertIsString($entity->getDescription());
    }
    public function testIsExtraLink()
    {
        $entity = new SQLIToolBoxEntityProperty();
        $this->assertIsString($entity->getExtraLink());
    }
    public function testGetDescription()
    {
        $entity = new SQLIToolBoxEntityProperty(description:  'expected_description');
        $this->assertEquals('expected_description', $entity->getDescription());
    }

    public function testGetChoices()
    {
        $entity = new SQLIToolBoxEntityProperty(choices: ["array"]);
        $this->assertEquals(1, count($entity->getChoices()));

    }
    public function testNullExtraLink()
    {
        $entity = new SQLIToolBoxEntityProperty();
        $this->assertNotNull($entity->getExtraLink());
    }
}
