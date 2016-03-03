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
        Assertion::range(
            $weight, self::MIN_WEIGHT, self::MAX_WEIGHT, 'Rank weight "%s" was expected to be at least "%d" and at most "%d".'
        );

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

    public function toString()
    {
        switch ($this->weight) {
            case 14:
                return 'ace';
            case 13:
                return 'king';
            case 12:
                return 'queen';
            case 11:
                return 'jack';
            default:
                return $this->weight;
        }
    }
}
