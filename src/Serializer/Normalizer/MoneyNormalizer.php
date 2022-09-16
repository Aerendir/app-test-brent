<?php

namespace App\Serializer\Normalizer;

use App\Model\DecimalValue;
use SerendipityHQ\Component\ValueObjects\Money\MoneyInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class MoneyNormalizer implements NormalizerInterface
{

    public function normalize($object, $format = null, array $context = []): float|int
    {
        if(false === $object instanceof MoneyInterface) {
            throw new \InvalidArgumentException();
        }

        return $object->getHumanAmount();
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof MoneyInterface;
    }

}
