<?php

declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Attributes\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class SQLIToolBoxEntityProperty implements SQLIToolBoxClassProperty
{
    /**
     * @param array|null $choices
     * @param string|null $extra_link
     */
    public function __construct(
        public bool $visible = true,
        public bool $readonly = false,
        public string $description = "",
        public ?array $choices = [],
        /**
         * @Enum({"content", "location", "tag"})
         */
        public ?string $extra_link = ''
    ) {
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
    public function getChoices(): ?array
    {
        return $this->choices;
    }

    /**
     * @return string
     */
    public function getExtraLink(): string
    {
        return $this->extra_link;
    }
}
