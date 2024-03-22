<?php

declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Entity\Doctrine;

use Doctrine\ORM\Mapping as ORM;
use SQLI\EzToolboxBundle\Annotations\Annotation as SQLIAdmin;
use stdClass;

/**
 * @ORM\Table(name="eboutique_parameter")
 * @ORM\Entity(repositoryClass="SQLI\EzToolboxBundle\Repository\Doctrine\ParameterRepository")
 * @SQLIAdmin\Entity(update=true,create=true,delete=true,description="Paramètrage",tabname="Param")
 */
class Parameter
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @SQLIAdmin\EntityProperty(readonly=true)
     */
    private int $id;
    /**
     * @var string
     * @ORM\Column(name="name", type="string", length=255)
     * @SQLIAdmin\EntityProperty(description="Nom du paramètre")
     */
    private string $name;
    /**
     * @var string
     * @ORM\Column(name="value", type="string", length=255)
     * @SQLIAdmin\EntityProperty(
     *     choices={"Activé": "enabled", "Désactivé": "disabled"},
     *     description="Paramètre activé ou non ?")
     */
    private string $value;
    /**
     * @var mixed
     *
     * @ORM\Column(name="params", type="object", nullable=true)
     * @SQLIAdmin\EntityProperty(
     *     visible=true,
     *     description="Données complémentaires sérialisées.
     * S'assurer de la validité avant sauvegarde avec https://fr.functions-online.com/unserialize.html")
     */
    private mixed $params;


    public function getId(): ?int
    {
        return $this->id;
    }


    public function setId(int $id): Parameter
    {
        $this->id = $id;

        return $this;
    }


    public function getName(): ?string
    {
        return $this->name;
    }


    public function setName(string $name): Parameter
    {
        $this->name = $name;

        return $this;
    }


    public function getValue(): ?string
    {
        return $this->value;
    }


    public function setValue(string $value): Parameter
    {
        $this->value = $value;

        return $this;
    }


    public function getParams(): mixed
    {
        return $this->params;
    }


    public function setParams(mixed $params): Parameter
    {
        $this->params = $params;

        return $this;
    }
}
