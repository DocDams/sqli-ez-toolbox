<?php

declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Serializer\SqliToolbox;

use SQLI\EzToolboxBundle\FieldType\SqliToolbox\Value;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class ValueNormalizer implements NormalizerInterface
{
    public function normalize($object, string $format = null, array $context = []): array
    {
        return [
            'className' => $object->getClassName(),
            'pkKey' => $object->getPkKey(),
            'pkValue' => $object->getPkValue()
        ];
    }

    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Value;
    }
}
