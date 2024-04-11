<?php

namespace SQLI\EzToolboxBundle\FieldType\SelectionFromEntity;


class ReverseTrans
{
    public function transformValue($group): Value
    {
        $transformedValue = new Value($group);

        return $transformedValue;
    }
}