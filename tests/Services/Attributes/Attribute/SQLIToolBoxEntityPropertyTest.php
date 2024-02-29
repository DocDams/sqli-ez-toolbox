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
        // Create a new instance of SQLIToolBoxEntity without passing any arguments
        $entity = new SQLIToolBoxEntityProperty();

        // Assert that default values are correctly set
        $this->assertFalse($entity->isReadonly());
        $this->assertTrue($entity->isVisible());
        $this->assertNull($entity->getChoices());
        $this->assertEquals('', $entity->getDescription());
        $this->assertEquals('', $entity->getExtraLink());

    }

    public function testSetVisible() {
        // Create a new instance of SQLIToolBoxEntity
        $entityAttribute = new SQLIToolBoxEntityProperty();
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
        $entity = new SQLIToolBoxEntityProperty();

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
        $entityAttribute = new SQLIToolBoxEntityProperty();
        // Set a new value for create
        $entityAttribute->choices = ['test'];
        // Assert that the value of create has been updated
        $this->assertIsArray($entityAttribute->getChoices());
        $this->assertEquals(1, count($entityAttribute->getChoices()));
    }

    public function testSetDescription()
    {
        // Create a new instance of YourClass
        $entity = new SQLIToolBoxEntityProperty();

        // Set a new description
        $description = "This is a new description";
        $entity->description = $description;
        // Assert that the description has been updated
        $this->assertEquals($description, $entity->getDescription());

    }

    public function testSetExtraLink()
    {
        // Create a new instance of YourClass
        $entity = new SQLIToolBoxEntityProperty();

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
        $entity = new SQLIToolBoxEntityProperty(); // Instanciez votre classe ici
        $this->assertTrue($entity->isVisible()); // Assurez-vous que isVisible() renvoie true
    }

    public function testIsReadonly()
    {
        $entity = new SQLIToolBoxEntityProperty(); // Instanciez votre classe ici
        $this->assertIsBool($entity->isReadonly()); // Assurez-vous que isReadonly() renvoie false
    }
    public function testIsChoices()
    {
        $entity = new SQLIToolBoxEntityProperty(choices: ['array']); // Instanciez votre classe ici
        $this->assertIsArray($entity->getChoices()); // Assurez-vous que isReadonly() renvoie false
    }
    public function testIsDescritpion()
    {
        $entity = new SQLIToolBoxEntityProperty(); // Instanciez votre classe ici
        $this->assertIsString($entity->getDescription()); // Assurez-vous que isReadonly() renvoie false
    }
    public function testIsExtraLink()
    {
        $entity = new SQLIToolBoxEntityProperty(); // Instanciez votre classe ici
        $this->assertIsString($entity->getExtraLink()); // Assurez-vous que isReadonly() renvoie false
    }
    public function testGetDescription()
    {
        $entity = new SQLIToolBoxEntityProperty(description:  'expected_description');
        // Instanciez votre classe ici
        $this->assertEquals('expected_description', $entity->getDescription()); // Assurez-vous que getDescription() retourne la description attendue
    }

    public function testGetChoices()
    {
        $entity = new SQLIToolBoxEntityProperty(choices: ["array"]);
        $this->assertEquals(1, count($entity->getChoices()));

    }
    public function testNullExtraLink()
    {
        $entity = new SQLIToolBoxEntityProperty(); // Instanciez votre classe ici
        $this->assertNotNull($entity->getExtraLink()); // Assurez-vous que getExtraLink() ne renvoie pas null
    }
}
