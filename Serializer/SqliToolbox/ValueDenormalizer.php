<?php
declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Serializer\SqliToolbox;

use SQLI\EzToolboxBundle\FieldType\SqliToolbox\Value;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ValueDenormalizer implements DenormalizerInterface
{
    public function denormalize($data, string $class, string $format = null, array $context = [])
    {
       if (is_array($data) && count($data)==3){
           return new $class($data[0],$data[1],$data[2]);
       }
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === Value::class;
    }
}