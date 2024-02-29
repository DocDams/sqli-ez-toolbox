<?php

declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Serializer\SqliToolbox;

use SQLI\EzToolboxBundle\FieldType\SqliToolbox\Value;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class ValueDenormalizer implements DenormalizerInterface
{
    public function denormalize($data, string $class, string $format = null, array $context = [])
    {
        if (is_array($data) && count($data) == 3) {
            return new $class($data['className'], $data['pkKey'], $data['pkValue']);
        }
    }

    public function supportsDenormalization($data, string $type, string $format = null)
    {
        return $type === Value::class;
    }
}
