<?php

namespace SQLI\EzToolboxBundle\Annotations\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @package SQLI\EzToolboxBundle\Annotations
 *
 * @Annotation
 * @Target({"PROPERTY"})
 */
final class EntityProperty implements SQLIPropertyAnnotation
{
    /** @var bool */
    public $visible = true;
    /** @var bool */
    public $readonly = false;
    /** @var string */
    public $description = "";
    /** @var array */
    public $choices = null;
    /**
     * @var string
     * @Enum({"content", "location", "tag"})
     */
    public $extra_link = null;


    public function __construct(
        bool $visible = true,
        bool $readonly = false,
        string $description = "",
        ?array $choices = null,
        ?string $extra_link = null
    ) {
        $this->visible = $visible;
        $this->readonly = $readonly;
        $this->description = $description;
        $this->choices = $choices;
        $this->extra_link = $extra_link;
    }


    /**
     * @return bool
     */
    public function isVisible(): bool
    {
        return $this->visible;
    }

    /**
     * @return bool
     */
    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return array|null
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @return string
     */
    public function getExtraLink()
    {
        return $this->extra_link;
    }
}
