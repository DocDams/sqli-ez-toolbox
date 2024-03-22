<?php

declare(strict_types=1);

namespace  SQLI\EzToolboxBundle\FieldType\SqliToolbox;

use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;

final class Value implements ValueInterface
{
    private ?string $className;

    private ?string $pkKey;

    private ?string $pkValue;

    public function __construct(?string $className = null, ?string $pkKey = null, ?string $pkValue = null)
    {
        $this->className = $className;
        $this->pkKey = $pkKey;
        $this->pkValue = $pkValue;
    }


    public function getClassName(): ?string
    {
        return $this->className;
    }


    public function setClassName(?string $className): void
    {
        $this->className = $className;
    }


    public function getPkValue(): ?string
    {
        return $this->pkValue;
    }


    public function setPkValue(?string $pkValue): void
    {
        $this->pkValue = $pkValue;
    }


    public function getPkKey(): ?string
    {
        return $this->pkKey;
    }


    public function setPkKey(?string $pkKey): void
    {
        $this->pkKey = $pkKey;
    }


    public function __toString()
    {
        return "({$this->className}, {$this->pkKey}, {$this->pkValue})";
    }
}
