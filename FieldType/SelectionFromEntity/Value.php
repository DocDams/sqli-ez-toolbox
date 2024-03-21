<?php
declare(strict_types=1);

namespace App\Services\FieldType\SelectionFromEntity;

use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;
use Symfony\Component\Validator\Constraints as Assert;


final class Value implements ValueInterface {

    #[Assert\NotBlank]
    private string $className;
    #[Assert\NotBlank]
    private string $valueAttribute;
    #[Assert\NotBlank]
    private string $labelAttribute;

    #[Assert\NotBlank]
    public array $selection;

    public function __construct(array $selection = [])
    {
        $this->selection = $selection;
    }


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

    public function getSelection(): array
    {
        return $this->selection;
    }

    public function setSelection(array $selection): void
    {
        $this->selection = $selection;
    }

    public function __toString()
    {
        return implode(',', $this->selection);
    }

}