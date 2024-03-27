<?php
declare(strict_types=1);

namespace  SQLI\EzToolboxBundle\FieldType\SelectionFromEntity;


use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;
use Symfony\Component\Validator\Constraints as Assert;


final class Value implements ValueInterface {

    #[Assert\NotBlank]
    private array $selection;
    public function __construct(array $selection = [])
    {
        $this->selection = $selection;
    }



    
    public function __toString()
    {
        return implode(',', $this->selection);
    }

}