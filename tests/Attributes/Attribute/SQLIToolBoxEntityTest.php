<?php

namespace SQLI\EzToolboxBundle\Tests\Attributes\Attribute;

use SQLI\EzToolboxBundle\Attributes\Attribute\SQLIToolBoxEntity;
use PHPUnit\Framework\TestCase;

class SQLIToolBoxEntityTest extends TestCase
{
    /**
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::__construct
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::isCreate
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::isUpdate
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::isDelete
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::getDescription
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::getMaxPerPage
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::isCsvExportable
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::getTabname
     */
    public function testPropertiesAreCorrectlyInitialized()
    {
        // Create a new instance of SQLIToolBoxEntity
        $entity = new SQLIToolBoxEntity(
            create: true,
            update: false,
            delete: true,
            description: 'Entity description',
            max_per_page: 20,
            csv_exportable: true,
            tabname: 'custom_tab'
        );

        // Assert that properties are initialized with correct values
        $this->assertTrue($entity->isCreate());
        $this->assertFalse($entity->isUpdate());
        $this->assertTrue($entity->isDelete());
        $this->assertEquals('Entity description', $entity->getDescription());
        $this->assertEquals(20, $entity->getMaxPerPage());
        $this->assertTrue($entity->isCsvExportable());
        $this->assertEquals('custom_tab', $entity->getTabname());
    }

    /**
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::__construct
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::isCreate
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::isUpdate
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::isDelete
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::getDescription
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::getMaxPerPage
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::isCsvExportable
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::getTabname
     */
    public function testDefaultValues()
    {
        // Create a new instance of SQLIToolBoxEntity without passing any arguments
        $entity = new SQLIToolBoxEntity();

        // Assert that default values are correctly set
        $this->assertFalse($entity->isCreate());
        $this->assertFalse($entity->isUpdate());
        $this->assertFalse($entity->isDelete());
        $this->assertEquals('', $entity->getDescription());
        $this->assertEquals(10, $entity->getMaxPerPage());
        $this->assertFalse($entity->isCsvExportable());
        $this->assertEquals('default', $entity->getTabname());
    }

    /**
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::__construct
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::getMaxPerPage
     */
    public function testNegativeMaxPerPage()
    {
        // Create a new instance of SQLIToolBoxEntity with negative max_per_page value
        $entity = new SQLIToolBoxEntity(max_per_page: -10);

        // Assert that max_per_page is set to its default value (10)
        $this->assertEquals(10, $entity->getMaxPerPage());
    }

    /**
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::__construct
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::getTabname
     */
    public function testDefaultTabname()
    {
        // Create a new instance of SQLIToolBoxEntity without specifying tabname
        $entityAttribute = new SQLIToolBoxEntity();

        // Assert that tabname is set to default value
        $this->assertEquals("default", $entityAttribute->getTabname());
    }

    /**
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::__set
     * @covers \SQLI\EzToolboxBundle\Attributes\SQLIToolBoxEntity::isCreate
     */
    public function testSetCreate()
    {
        // Create a new instance of SQLIToolBoxEntity
        $entityAttribute = new SQLIToolBoxEntity();

        // Set a new value for create
        $entityAttribute->create = true;

        // Assert that the value of create has been updated
        $this->assertTrue($entityAttribute->isCreate());
    }




}
