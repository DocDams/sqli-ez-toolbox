<?php

declare(strict_types=1);

namespace  SQLI\EzToolboxBundle\FieldType\SqliToolbox;

use eZ\Publish\SPI\FieldType\Value as ValueInterface;

final class Value implements ValueInterface
{
    /** @var string|null */
    private $className;
    /** @var string|null */
    private $pkKey;

    /** @var int|null */
    private $pkValue;

    public function __construct(?string $className = null, ?string $pkKey = null ,?int $pkValue = null)
    {
        $this->className = $className;
        $this->pkKey = $pkKey;
        $this->pkValue = $pkValue;
    }

    /**
     * @return string|null
     */
    public function getClassName(): ?string
    {
        return $this->className;
    }

    /**
     * @param string|null $className
     */
    public function setClassName(?string $className): void
    {
        $this->className = $className;
    }

    /**
     * @return int|null
     */
    public function getPkValue(): ?int
    {
        return $this->pkValue;
    }

    /**
     * @param int|null $pkValue
     */
    public function setPkValue(?int $pkValue): void
    {
        $this->pkValue = $pkValue;
    }

    /**
     * @return string|null
     */
    public function getPkKey(): ?string
    {
        return $this->pkKey;
    }

    /**
     * @param string|null $pkKey
     */
    public function setPkKey(?string $pkKey): void
    {
        $this->pkKey = $pkKey;
    }


    public function __toString()
    {
        return "({$this->className}, {$this->pkKey}, {$this->pkValue})";
    }
}