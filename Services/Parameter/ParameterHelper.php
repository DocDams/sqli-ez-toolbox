<?php

declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Services\Parameter;

class ParameterHelper
{
    public function __construct(private readonly ParameterHandlerRepository $parameterHandlerRepository)
    {
    }
}
