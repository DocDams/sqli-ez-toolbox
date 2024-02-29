<?php

namespace SQLI\EzToolboxBundle\tests\Annotations\Annotation;

use PHPUnit\Framework\TestCase;
use SQLI\EzToolboxBundle\Annotations\Annotation\Entity;

class EntityTest extends TestCase
{
    /**
     * @covers \SQLI\EzToolboxBundle\Annotations\Annotation\Entity::__construct
     */
    public function testDefaultValues(): void
    {
        // Create a new instance of Entity annotation without passing any arguments
        $entity = new Entity();

        // Assert that default values are correctly set
        $this->assertFalse($entity->isCreate());
        $this->assertFalse($entity->isUpdate());
        $this->assertFalse($entity->isDelete());
        $this->assertEquals('', $entity->getDescription());
        $this->assertEquals(10, $entity->getMaxPerPage());
        $this->assertFalse($entity->isCSVExportable());
        $this->assertEquals('default', $entity->getTabname());
    }


    /**
     * @covers \SQLI\EzToolboxBundle\Annotations\Annotation\Entity::__construct
     * @covers \SQLI\EzToolboxBundle\Annotations\Annotation\Entity::getTabname
     */
    public function testDefaultTabname(): void
    {
        // Create a new instance of Entity annotation without specifying tabname
        $entity = new Entity();

        // Assert that tabname is set to default value
        $this->assertEquals("default", $entity->getTabname());
    }

    /**
     * @covers \SQLI\EzToolboxBundle\Annotations\Annotation\Entity::__set
     * @covers \SQLI\EzToolboxBundle\Annotations\Annotation\Entity::isCreate
     */
    public function testSetCreate(): void
    {
        // Create a new instance of Entity annotation
        $entity = new Entity();

        // Set a new value for create
        $entity->create = true;

        // Assert that the value of create has been updated
        $this->assertTrue($entity->isCreate());
    }

    /**
     * @covers \SQLI\EzToolboxBundle\Annotations\Annotation\Entity::__set
     * @covers \SQLI\EzToolboxBundle\Annotations\Annotation\Entity::isUpdate
     */
    public function testSetUpdate(): void
    {
        // Create a new instance of Entity annotation
        $entity = new Entity();

        // Set a new value for update
        $entity->update = true;

        // Assert that the value of update has been updated
        $this->assertTrue($entity->isUpdate());
    }

    /**
     * @covers \SQLI\EzToolboxBundle\Annotations\Annotation\Entity::__set
     * @covers \SQLI\EzToolboxBundle\Annotations\Annotation\Entity::isDelete
     */
    public function testSetDelete(): void
    {
        // Create a new instance of Entity annotation
        $entity = new Entity();

        // Set a new value for delete
        $entity->delete = true;

        // Assert that the value of delete has been updated
        $this->assertTrue($entity->isDelete());
    }

    /**
     * @covers \SQLI\EzToolboxBundle\Annotations\Annotation\Entity::__set
     * @covers \SQLI\EzToolboxBundle\Annotations\Annotation\Entity::getDescription
     */
    public function testSetDescription(): void
    {
        // Create a new instance of Entity annotation
        $entity = new Entity();

        // Set a new value for description
        $entity->description = "New entity description";

        // Assert that the value of description has been updated
        $this->assertEquals("New entity description", $entity->getDescription());
    }
}
