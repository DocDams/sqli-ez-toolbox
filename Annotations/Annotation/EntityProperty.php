<?php

declare(strict_types=1);

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
    public bool $visible = true;
    /** @var bool */
    public bool $readonly = false;
    /** @var string */
    public string $description = "";
    public ?array $choices = null;
    /**
     * @var ?string
     * @Enum({"content", "location", "tag"})
     */
    public ?string $extra_link = null;


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
    public function getChoices(): ?array
    {
        return $this->choices;
    }

    /**
     * @return string|null
     */
    public function getExtraLink(): ?string
    {
        return $this->extra_link;
    }
}
