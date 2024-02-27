<?php

namespace SQLI\EzToolboxBundle\tests\Services\Attributes\Attribute;

use PHPUnit\Framework\TestCase;
use SQLI\EzToolboxBundle\Attributes\Attribute\SQLIToolBoxEntityProperty;
use SQLI\EzToolboxBundle\Attributes\Attribute\SQLIToolBoxEntity;

class SQLIToolBoxEntityPropertyTest extends TestCase
{

    public function test__construct()
    {
        $entity = new SQLIToolBoxEntityProperty(visible: true, readonly: true, description: "testDescription", choices: [], extra_link: "testExtra");
        $this->assertInstanceOf(SQLIToolBoxEntityProperty::CLASS,$entity);

    }
    public function test__constructFalse()
    {
        $entity = new SQLIToolBoxEntity( update: false, delete: true, description: 'Entity description', max_per_page: 20, csv_exportable: true, tabname: 'custom_tab');
        $this->assertNotInstanceOf(SQLIToolBoxEntityProperty::CLASS,$entity);

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
    public function testGetChoices()
    {
        $entity = new SQLIToolBoxEntityProperty(choices: ["array"]);
        $this->assertEquals(1, count($entity->getChoices()));

    }

    public function testIsVisible()
    {

    }

    public function testIsReadonly()
    {

    }

    public function testGetDescription()
    {

    }

    public function testGetExtraLink()
    {

    }
}
