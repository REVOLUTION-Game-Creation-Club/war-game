<?php

namespace WarGame\Domain\Card;

final class Suit
{
    const CLUBS = 'clubs';
    const DIAMONDS = 'diamonds';
    const HEARTS = 'hearts';
    const SPADES = 'spades';

    private $name;

    private function __construct($name)
    {
        $this->name = $name;
    }

    public static function clubs()
    {
        return new self(self::CLUBS);
    }

    public static function diamonds()
    {
        return new self(self::DIAMONDS);
    }

    public static function hearts()
    {
        return new self(self::HEARTS);
    }

    public static function spades()
    {
        return new self(self::SPADES);
    }

    public function getName()
    {
        return $this->name;
    }

    public static function getSuits()
    {
        return [self::CLUBS, self::DIAMONDS, self::HEARTS, self::SPADES];
    }
}
