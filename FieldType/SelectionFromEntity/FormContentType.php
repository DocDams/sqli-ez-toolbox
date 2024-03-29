<?php

declare(strict_types=1);

namespace SQLI\EzToolboxBundle\FieldType\SelectionFromEntity;


use Symfony\Component\Validator\Constraints as Assert;


final class FormContentType
{

    #[Assert\NotBlank]
    private string $className;
    #[Assert\NotBlank]
    private string $valueAttribute;
    #[Assert\NotBlank]
    private string $labelAttribute;
    private string $filer;
    /**
     * @return mixed
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param mixed $className
     */
    public function setClassName($className): void
    {
        $this->className = $className;
    }

    /**
     * @return mixed
     */
    public function getValueAttribute()
    {
        return $this->valueAttribute;
    }

    /**
     * @param mixed $valueAttribute
     */
    public function setValueAttribute($valueAttribute): void
    {
        $this->valueAttribute = $valueAttribute;
    }

    /**
     * @return mixed
     */
    public function getLabelAttribute()
    {
        return $this->labelAttribute;
    }

    /**
     * @param mixed $labelAttribute
     */
    public function setLabelAttribute($labelAttribute): void
    {
        $this->labelAttribute = $labelAttribute;
    }

}