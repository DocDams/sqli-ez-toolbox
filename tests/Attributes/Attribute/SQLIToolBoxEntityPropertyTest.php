<?php

declare(strict_types=1);

namespace SQLI\EzToolboxBundle\tests\Services\Attributes\Attribute;

use PHPUnit\Framework\TestCase;
use SQLI\EzToolboxBundle\Attributes\Attribute\SQLIToolBoxEntityProperty;

class SQLIToolBoxEntityPropertyTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $entity = new SQLIToolBoxEntityProperty();
        $this->assertFalse($entity->isReadonly());
        $this->assertTrue($entity->isVisible());
        $this->assertIsArray($entity->getChoices());
        $this->assertEquals('', $entity->getDescription());
        $this->assertEquals('', $entity->getExtraLink());
    }

    public function testSetVisible(): void
    {
        $entityAttribute = new SQLIToolBoxEntityProperty();
        $entityAttribute->visible = true;
        $this->assertTrue($entityAttribute->isVisible());
        $entityAttribute->visible = false;
        $this->assertFalse($entityAttribute->isVisible());
    }
    public function testSetReadonly(): void
    {
        $entity = new SQLIToolBoxEntityProperty();
        $entity->readonly = true;
        $this->assertTrue($entity->isReadonly());
        $entity->readonly = false;
        $this->assertFalse($entity->isReadonly());
    }
    public function testSetChoices(): void
    {
        $entityAttribute = new SQLIToolBoxEntityProperty();
        $entityAttribute->choices = ['test'];
        $this->assertIsArray($entityAttribute->getChoices());
        $this->assertEquals(1, count($entityAttribute->getChoices()));
    }

    public function testSetDescription(): void
    {
        $entity = new SQLIToolBoxEntityProperty();
        $description = "This is a new description";
        $entity->description = $description;

        $this->assertEquals($description, $entity->getDescription());
    }

    public function testSetExtraLink(): void
    {
        $entity = new SQLIToolBoxEntityProperty();
        $link = "http://example.com";
        $entity->extra_link = $link;
        $this->assertEquals($link, $entity->getExtraLink());
        $link = '';
        $entity->extra_link = $link;
        $this->assertEmpty($link, $entity->getDescription());
    }
    public function testIsVisible(): void
    {
        $entity = new SQLIToolBoxEntityProperty();
        $this->assertTrue($entity->isVisible());
    }

    public function testIsReadonly(): void
    {
        $entity = new SQLIToolBoxEntityProperty();
        $this->assertIsBool($entity->isReadonly());
    }
    public function testIsChoices(): void
    {
        $entity = new SQLIToolBoxEntityProperty(choices: ['array']);
        $this->assertIsArray($entity->getChoices());
    }
    public function testIsDescritpion(): void
    {
        $entity = new SQLIToolBoxEntityProperty();
        $this->assertIsString($entity->getDescription());
    }
    public function testIsExtraLink(): void
    {
        $entity = new SQLIToolBoxEntityProperty();
        $this->assertIsString($entity->getExtraLink());
    }
    public function testGetDescription(): void
    {
        $entity = new SQLIToolBoxEntityProperty(description:  'expected_description');
        $this->assertEquals('expected_description', $entity->getDescription());
    }

    public function testGetChoices(): void
    {
        $entity = new SQLIToolBoxEntityProperty(choices: ["array"]);
        $this->assertEquals(1, count($entity->getChoices()));
    }
    public function testNullExtraLink(): void
    {
        $entity = new SQLIToolBoxEntityProperty();
        $this->assertNotNull($entity->getExtraLink());
    }
}
