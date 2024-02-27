<?php

namespace SQLI\EzToolboxBundle\Attributes\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
final class SQLIToolBoxEntityProperty implements SQLIToolBoxClassProperty
{

    /** @var bool */
    public $visible ;

    /** @var bool */
    public $readonly ;

    /** @var string */
    public $description;

    /** @var array */
    public $choices ;

    /**
     * @var string
     * @Enum({"content", "location", "tag"})
     */
    public $extra_link ;

    /**
     * @param bool $visible
     * @param bool $readonly
     * @param string $description
     * @param array|null $choices
     * @param string|null $extra_link
     */
    public function __construct(bool $visible= true, bool $readonly= false, string $description = "", ?array $choices= null, ?string $extra_link= "")
    {
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




