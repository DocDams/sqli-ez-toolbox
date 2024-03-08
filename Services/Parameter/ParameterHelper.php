<?php

namespace SQLI\EzToolboxBundle\Services\Parameter;

class ParameterHelper
{
    public function __construct(private readonly ParameterHandlerRepository $parameterHandlerRepository)
    {
    }
}
