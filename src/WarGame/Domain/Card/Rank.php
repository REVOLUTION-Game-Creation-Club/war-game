<?php

namespace WarGame\Domain\Card;

use Assert\Assertion;

final class Rank
{
    private $weight;

    public function __construct($weight)
    {
        Assertion::range($weight, 2, 15);

        $this->weight = $weight;
    }

    public function getWeight()
    {
        return $this->weight;
    }
}
