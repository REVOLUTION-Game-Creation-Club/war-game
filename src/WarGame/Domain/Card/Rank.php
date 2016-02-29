<?php

namespace WarGame\Domain\Card;

use Assert\Assertion;

final class Rank
{
    const MIN_WEIGHT = 2;
    const MAX_WEIGHT = 14;

    private $weight;

    public function __construct($weight)
    {
        Assertion::range($weight, self::MIN_WEIGHT, self::MAX_WEIGHT);

        $this->weight = $weight;
    }

    public function getWeight()
    {
        return $this->weight;
    }
}
