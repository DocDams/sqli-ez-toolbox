SQLI Entities Manager
========================

### Configuration

Configure directories (and namespaces if not according to PSR-0 rules) entities to lookup :

```yml
sqli_ez_toolbox:
    entities:
        - { directory: 'Acme/AcmeBundle/Entity/Doctrine' }
        - { directory: 'Acme/AcmeBundle2/Entity/Doctrine', namespace: 'Acme\AcmeBundle2NoPSR0\ORM\Doctrine' }
```
Use "~" if the namespace of your classes observe PSR-0 rules or specify directory which contains them.

### Annotations on entities

```php
<?php
namespace Acme\AcmeBundle\Entity\Doctrine;

use SQLI\EzToolboxBundle\Annotations\Annotation as SQLIAdmin;

/**
* Class MyEntity
 * 
 * @package Acme\AcmeBundle\Entity\Doctrine
 * @ORM\Table(name="my_entity")
 * @ORM\Entity(repositoryClass="Acme\AcmeBundle\Repository\Doctrine\MyEntityRepository")
 * @SQLIAdmin\Entity(update=true,
 *                   create=true,
 *                   delete=false,
 *                   csv_exportable=false,
 *                   max_per_page=5,
 *                   tabname="other_tab"
 *                   description="Describe your entity")
 */
class MyEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="id",type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="data",type="string")
     * @SQLIAdmin\EntityProperty(visible=false)
     */
    private $data;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="text",type="string")
     * @SQLIAdmin\EntityProperty(description="Describe property of your entity",readonly=true)
     */
    private $text;
    
    /**
     * @var string
     * @ORM\Column(name="select",type="int")
     * @SQLIAdmin\EntityProperty(choices={"First choice": 1, "Second choice": 2})
     */
    private $select;
    
    /**
     * @var integer
     * @ORM\Column(name="content_id",type="int")
     * @SQLIAdmin\EntityProperty(extra_link="location") 
    */
    private $contentID;
    
    // ...
    public function getId()
    {
        return $this->id;
    }
    
    public function getData() : ?string
    {
        return $this->data;
    }
    
    public function getText() : string 
    {
        return $this->text ?: '';
    }
    
    public function getSelect() : int
    {
        return $this->select;
    }
    
    public function getContentID(): int
    {
        return $this->contentID;
    }
}
```


### Annotations properties

Class annotation `Entity` has following properties :
- **description** Description
- **update** Allow update of a line in table
- **delete** Allow deletion of a line in table
- **create** Allow creation of new line in table
- **max_per_page** Number of elements per page (Pagerfanta)
- **csv_exportable** Allow data CSV export for the entity
- **tabname** Group this entity in a tab under top menu instead of default tab

Property annotation `EntityProperty` has following properties :
- **description** Description
- **visible** Display column
- **readonly** Disallow modifications in edit form
- **choices** An hash relayed to [ChoiceType](https://symfony.com/doc/current/reference/forms/types/choice.html#choices)
- **extra_link** Use value as contentID or locationID or tagID (required [Netgen/TagsBundle](https://packagist.org/packages/netgen/tagsbundle)) to create a link in eZPlatform Back-Office


### Supported types

List of supported Doctrine types :
- string
- text
- integer
- float
- decimal
- boolean
- date
- datetime
- array (Using [serialization](https://www.php.net/manual/en/language.oop5.serialization.php))
- object (Using [serialization](https://www.php.net/manual/en/language.oop5.serialization.php))

**NOTICE** : Be careful if you choose to specify the return type on getters : in creation mode, getters will return 'null' so please provide a default value or nullable in type of return (see getter in above class example)


### Tab

Specifying class annotation `tabname` for an entity will create a new tab under main top menu.  
Label for this tab can be define in translation domain `sqli_admin` with this key :  
sqli_admin__menu_entities_tab__***tabname***

Traduction for `default` tab :

sqli_admin__menu_entities_tab__default: "Entités Doctrine"