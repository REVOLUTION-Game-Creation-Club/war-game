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

    public static function jack()
    {
        return new self(11);
    }

    public static function queen()
    {
        return new self(12);
    }

    public static function king()
    {
        return new self(13);
    }

    public static function ace()
    {
        return new self(14);
    }

    public function getWeight()
    {
        return $this->weight;
    }
}
