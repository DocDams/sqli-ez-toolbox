<?php
declare(strict_types=1);

namespace  SQLI\EzToolboxBundle\FieldType\SelectionFromEntity;


use Ibexa\Contracts\Core\FieldType\Value as ValueInterface;
class Value implements ValueInterface
{
    public array $selection;

    public function __construct(array $selection = [])
    {
        $this->selection = $selection;
    }

    public function getSelection(): array
    {
        return $this->selection;
    }
//  public function convertArray(Object $array) : string {
//        $resultArray = [];
//        foreach ($array as $elem) {
//            $objArray = array($array);
//            $resultArray[] = implode(',', $objArray);
//        }
//
//        return implode('|', $resultArray);
//   }
    public function __toString(): string
    {
        return 'selectionFRomEntity';
    }
}